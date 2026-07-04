<?php

namespace App\CentralLogics;

use App\Model\MatrixIncentiveLog;
use App\Model\MatrixLevel;
use App\Model\MatrixMember;
use App\User;

class MatrixLogic
{
    /**
     * Maximum children per parent in the matrix tree (4×4 rule).
     */
    const MAX_CHILDREN = 4;

    /**
     * Find the first node in the tree (BFS) that has fewer than MAX_CHILDREN.
     * Used for auto-placing un-referred members under the company root.
     */
    private static function findAvailableParent(int $rootId): int
    {
        $queue = [$rootId];
        $visited = [];

        while (!empty($queue)) {
            $currentId = array_shift($queue);

            if (in_array($currentId, $visited)) {
                continue;
            }
            $visited[] = $currentId;

            $childrenCount = MatrixMember::where('parent_id', $currentId)->count();
            if ($childrenCount < self::MAX_CHILDREN) {
                return $currentId;
            }

            $children = MatrixMember::where('parent_id', $currentId)
                ->orderBy('position')
                ->pluck('user_id');

            foreach ($children as $childId) {
                $queue[] = $childId;
            }
        }

        return $rootId;
    }

    /**
     * Add a new member to the matrix tree.
     *
     * When $parentId is null (no referral), auto-place the member in the first
     * available slot under the company root using the 4×4 matrix rule.
     *
     * When $parentId is provided, place directly under that parent as a child.
     *
     * Returns MatrixMember on success, or error string on failure.
     */
    public static function addMemberToMatrix($userId, $parentId = null): MatrixMember|string
    {
        if (!$parentId) {
            // No referral — auto-place in the company tree (4×4 rule)
            $rootId = Helpers::get_business_settings('company_root_id') ?? 1;
            $parentId = self::findAvailableParent($rootId);
        }

        $parent = User::find($parentId);
        if (!$parent) {
            return 'Parent user not found';
        }

        $parentMember = MatrixMember::firstOrCreate(
            ['user_id' => $parentId],
            [
                'parent_id' => null,
                'position' => null,
                'depth' => 0,
                'path' => (string)$parentId,
            ]
        );

        $childrenCount = MatrixMember::where('parent_id', $parentId)->count();
        $position = $childrenCount + 1;
        $depth = $parentMember->depth + 1;
        $path = $parentMember->path . '/' . $userId;

        $member = MatrixMember::create([
            'user_id' => $userId,
            'parent_id' => $parentId,
            'position' => $position,
            'depth' => $depth,
            'path' => $path,
        ]);

        self::recalculateTeamCounts($parentId);

        self::checkAndAwardIncentives($parentId);

        return $member;
    }

    /**
     * Count total team members for a user using materialized path.
     */
    public static function getTotalTeamCount($userId): int
    {
        $member = MatrixMember::where('user_id', $userId)->first();
        if (!$member || !$member->path) {
            return 0;
        }

        return MatrixMember::where(function ($query) use ($member, $userId) {
            $query->where('path', 'like', $member->path . '/%')
                  ->orWhere('path', 'like', '%/' . $userId . '/%');
        })->where('user_id', '!=', $userId)->count();
    }

    /**
     * Check and award incentives for a user based on recursive matrix qualification.
     *
     * Level 1 (Bronze):     4 active direct members
     * Level 2 (Silver):     Level 1 + 4+ directs each at Level 1+
     * Level 3 (Gold):       Level 2 + 4+ directs each at Level 2+
     * (and so on recursively)
     */
    public static function checkAndAwardIncentives($userId): void
    {
        $user = User::find($userId);
        if (!$user) {
            return;
        }

        // Require at least 4 active direct referrals
        $directChildren = MatrixMember::where('parent_id', $userId)->get();
        if ($directChildren->count() < 4) {
            return;
        }
        foreach ($directChildren as $child) {
            $childUser = User::find($child->user_id);
            if (!$childUser || !$childUser->is_member) {
                return;
            }
        }

        $levels = MatrixLevel::active()->orderBy('level')->get();
        $awardedLevel = 0;
        $awardedPosition = null;

        foreach ($levels as $level) {
            if ($level->level == 1) {
                $qualifies = true; // Level 1: just need 4 active directs
            } else {
                // Level N: need 4+ directs who have reached Level N-1
                $prevLevel = $level->level - 1;
                $directsAtPrevLevel = 0;
                foreach ($directChildren as $child) {
                    $childUser = User::find($child->user_id);
                    if ($childUser && $childUser->matrix_level >= $prevLevel) {
                        $directsAtPrevLevel++;
                    }
                }
                $qualifies = ($directsAtPrevLevel >= 4);
            }

            if ($qualifies) {
                $existingLog = MatrixIncentiveLog::where('user_id', $userId)
                    ->where('matrix_level_id', $level->id)
                    ->first();

                if (!$existingLog) {
                    MatrixIncentiveLog::create([
                        'user_id' => $userId,
                        'matrix_level_id' => $level->id,
                        'level' => $level->level,
                        'position_name' => $level->position_name,
                        'amount' => $level->incentive_amount,
                        'total_team_members' => $user->total_team_members,
                        'status' => 'credited',
                    ]);

                    CustomerLogic::create_wallet_transaction(
                        $userId,
                        $level->incentive_amount,
                        'add_fund',
                        'matrix_incentive_level_' . $level->level
                    );
                }

                if ($level->level > $awardedLevel) {
                    $awardedLevel = $level->level;
                    $awardedPosition = $level->position_name;
                }
            } else {
                break; // Can't skip levels — stop checking higher ones
            }
        }

        if ($awardedLevel > 0) {
            $user->matrix_level = $awardedLevel;
            $user->matrix_position = $awardedPosition;
            $user->save();
        }
    }

    /**
     * Get the matrix tree for a user up to a certain depth.
     */
    public static function getMatrixTree($userId, $depth = 1): array
    {
        $member = MatrixMember::with(['user'])->where('user_id', $userId)->first();
        if (!$member) {
            return [];
        }

        return self::buildTree($member, $depth);
    }

    /**
     * Recursively build the tree structure.
     */
    private static function buildTree($member, int $maxDepth, int $currentDepth = 0): array
    {
        if ($currentDepth > $maxDepth || !$member) {
            return [];
        }

        $node = [
            'id' => $member->user_id,
            'name' => $member->user ? ($member->user->f_name . ' ' . $member->user->l_name) : 'Unknown',
            'phone' => $member->user ? $member->user->phone : '',
            'referral_code' => $member->user ? ($member->user->referral_code ?? '—') : '—',
            'is_member' => $member->user ? (bool)$member->user->is_member : false,
            'position' => $member->position,
            'depth' => $member->depth,
            'matrix_level' => $member->user ? $member->user->matrix_level : 0,
            'matrix_position' => $member->user ? $member->user->matrix_position : '—',
        ];

        if ($currentDepth < $maxDepth) {
            $children = MatrixMember::with(['user'])
                ->where('parent_id', $member->user_id)
                ->orderBy('position')
                ->get();

            $node['children'] = [];
            foreach ($children as $child) {
                $childNode = self::buildTree($child, $maxDepth, $currentDepth + 1);
                // Embed parent info so every card can display "sponsored by"
                $childNode['parent_id'] = $member->user_id;
                $childNode['parent_name'] = $member->user ? ($member->user->f_name . ' ' . $member->user->l_name) : null;
                $childNode['parent_phone'] = $member->user ? $member->user->phone : null;
                $childNode['parent_matrix_position'] = $member->user ? $member->user->matrix_position : null;
                $node['children'][] = $childNode;
            }
        }

        return $node;
    }

    /**
     * Get user's matrix status (level, position, team count, next milestone).
     */
    public static function getMatrixStatus($userId): array
    {
        $user = User::find($userId);
        if (!$user) {
            return [];
        }

        $teamCount = $user->total_team_members;

        $currentLevel = $user->matrix_level > 0
            ? MatrixLevel::active()->where('level', $user->matrix_level)->first()
            : null;

        // Check direct referrals activation and their levels
        $directChildren = MatrixMember::where('parent_id', $userId)->get();
        $directReferrals = $directChildren->map(function ($child) {
            $cu = User::find($child->user_id);
            return [
                'id' => $child->user_id,
                'position' => $child->position,
                'is_member' => $cu ? (bool)$cu->is_member : false,
                'name' => $cu ? ($cu->f_name . ' ' . $cu->l_name) : 'Unknown',
                'matrix_level' => $cu ? $cu->matrix_level : 0,
                'matrix_position' => $cu ? $cu->matrix_position : null,
            ];
        })->toArray();

        // Recursive qualification: for next level, count directs at required previous level
        $nextLevelNum = $user->matrix_level + 1;
        $nextLevelRecursive = MatrixLevel::active()
            ->where('level', $nextLevelNum)
            ->first();
        $requiredDirectsWithPrevLevel = $nextLevelNum <= 1 ? count($directReferrals) : 0;
        $directsReadyForNext = 0;
        if ($nextLevelRecursive && $nextLevelNum > 1) {
            $prevRequired = $nextLevelNum - 1;
            $directsReadyForNext = count(array_filter($directReferrals, fn($r) => $r['matrix_level'] >= $prevRequired));
            $requiredDirectsWithPrevLevel = 4;
        } elseif ($nextLevelRecursive && $nextLevelNum == 1) {
            $directsReadyForNext = count(array_filter($directReferrals, fn($r) => $r['is_member']));
            $requiredDirectsWithPrevLevel = 4;
        }

        // Calculate remaining_members: number of new members needed at the deepest level
        // For level N, the deepest required depth = N, needing 4^N = required_members
        $remainingMembers = 0;
        if ($nextLevelRecursive) {
            $member = MatrixMember::where('user_id', $userId)->first();
            if ($member) {
                $deepestDepth = $nextLevelNum; // e.g., Gold (lv3) needs members at depth 3
                $existingAtDepth = MatrixMember::where(function ($q) use ($member, $userId) {
                    $q->where('path', 'like', $member->path . '/%')
                      ->orWhere('path', 'like', '%/' . $userId . '/%');
                })->where('depth', $deepestDepth)
                  ->where('user_id', '!=', $userId)
                  ->count();
                $remainingMembers = max(0, $nextLevelRecursive->required_members - $existingAtDepth);
            }
        }

        $allDirectActive = count($directReferrals) >= 4 && collect($directReferrals)->every(fn($r) => $r['is_member']);
        $incentivesEligible = $allDirectActive && ($user->matrix_level >= 1 || count($directReferrals) >= 4);

        return [
            'user_id' => $user->id,
            'name' => $user->f_name . ' ' . $user->l_name,
            'is_member' => (bool)$user->is_member,
            'current_level' => $user->matrix_level,
            'current_position' => $user->matrix_position,
            'total_team_members' => $teamCount,
            'direct_referrals_filled' => count($directReferrals),
            'direct_referrals_active' => count(array_filter($directReferrals, fn($r) => $r['is_member'])),
            'incentives_eligible' => $incentivesEligible,
            'direct_referrals' => $directReferrals,
            'current_level_info' => $currentLevel ? [
                'level' => $currentLevel->level,
                'position_name' => $currentLevel->position_name,
                'required_members' => $currentLevel->required_members,
                'incentive_amount' => $currentLevel->incentive_amount,
            ] : null,
            'next_level' => $nextLevelRecursive ? [
                'level' => $nextLevelRecursive->level,
                'position_name' => $nextLevelRecursive->position_name,
                'incentive_amount' => $nextLevelRecursive->incentive_amount,
                'remaining_members' => $remainingMembers,
                'condition' => $nextLevelNum > 1
                    ? "Need {$requiredDirectsWithPrevLevel}+ directs at Level {$prevRequired} (" . ($currentLevel->position_name ?? 'N/A') . "+)"
                    : "Need {$requiredDirectsWithPrevLevel}+ active direct referrals",
                'directs_ready' => $directsReadyForNext,
                'directs_required' => $requiredDirectsWithPrevLevel,
            ] : null,
        ];
    }

    /**
     * Get direct referrals (team members) for a user.
     */
    public static function getTeam($userId): array
    {
        $members = MatrixMember::with(['user'])
            ->where('parent_id', $userId)
            ->orderBy('position')
            ->get();

        return $members->map(function ($member) {
            return [
                'id' => $member->user_id,
                'name' => $member->user ? ($member->user->f_name . ' ' . $member->user->l_name) : 'Unknown',
                'phone' => $member->user ? $member->user->phone : '',
                'email' => $member->user ? $member->user->email : '',
                'position' => $member->position,
                'is_member' => $member->user ? (bool)$member->user->is_member : false,
                'joined_at' => $member->created_at,
            ];
        })->toArray();
    }

    /**
     * Migrate all matrix members from the old company root to a new one.
     * Called automatically when admin changes the company_root_id setting.
     *
     * @param int $oldRootId The previous company_root_id (before saving the new one)
     * @param int $newRootId The new company_root_id
     */
    public static function migrateCompanyRoot(int $oldRootId, int $newRootId): void
    {
        if ($oldRootId === $newRootId) {
            return;
        }

        $oldRootMember = MatrixMember::where('user_id', $oldRootId)->first();
        $oldRootPath = $oldRootMember ? $oldRootMember->path : (string)$oldRootId;

        // Ensure new root has a MatrixMember record (create if fresh user)
        $newRootMember = MatrixMember::firstOrCreate(
            ['user_id' => $newRootId],
            ['parent_id' => null, 'position' => null, 'depth' => 0, 'path' => (string)$newRootId]
        );
        $newRootPath = $newRootMember->path;

        // Collect descendant IDs BEFORE any path changes (to avoid query misses)
        $descendantIds = MatrixMember::where('path', 'like', $oldRootPath . '/%')
            ->where('user_id', '!=', $oldRootId)
            ->where('user_id', '!=', $newRootId)
            ->pluck('id');

        // Migrate direct children of old root (excluding new root if it was a child)
        $directChildren = MatrixMember::where('parent_id', $oldRootId)
            ->where('user_id', '!=', $newRootId)
            ->orderBy('position')
            ->get();

        foreach ($directChildren as $child) {
            $child->parent_id = $newRootId;
            $relativePath = substr($child->path, strlen($oldRootPath) + 1);
            $child->path = $newRootPath . '/' . $relativePath;
            $child->save();
        }

        // Migrate all deeper descendants
        if ($descendantIds->isNotEmpty()) {
            $descendants = MatrixMember::whereIn('id', $descendantIds)->get();
            foreach ($descendants as $descendant) {
                $relativePath = substr($descendant->path, strlen($oldRootPath) + 1);
                $descendant->path = $newRootPath . '/' . $relativePath;
                $descendant->save();
            }
        }

        // Recalculate team counts for the new root (walks up ancestors too)
        self::recalculateTeamCounts($newRootId);

        // Reset old root's team count
        User::where('id', $oldRootId)->update(['total_team_members' => 0]);

        // Clean up old root's MatrixMember if it was a pure root node
        if ($oldRootMember && $oldRootMember->parent_id === null) {
            $remainingChildren = MatrixMember::where('parent_id', $oldRootId)->count();
            if ($remainingChildren === 0) {
                $oldRootMember->delete();
            }
        }
    }

    /**
     * Recalculate team counts for a user and all ancestors,
     * and check incentive eligibility for each.
     */
    public static function recalculateTeamCounts($userId): void
    {
        $visited = [];
        $currentId = $userId;

        while ($currentId && !in_array($currentId, $visited)) {
            $visited[] = $currentId;
            $count = self::getTotalTeamCount($currentId);

            User::where('id', $currentId)->update([
                'total_team_members' => $count,
            ]);

            self::checkAndAwardIncentives($currentId);

            $member = MatrixMember::where('user_id', $currentId)->first();
            $currentId = $member ? $member->parent_id : null;
        }
    }
}

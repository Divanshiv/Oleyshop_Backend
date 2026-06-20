<?php

namespace App\CentralLogics;

use App\Model\MatrixIncentiveLog;
use App\Model\MatrixLevel;
use App\Model\MatrixMember;
use App\User;

class MatrixLogic
{
    /**
     * Add a new member to the matrix tree under a parent user.
     * Returns MatrixMember on success, or error string on failure.
     */
    public static function addMemberToMatrix($userId, $parentId): MatrixMember|string
    {
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
                $node['children'][] = self::buildTree($child, $maxDepth, $currentDepth + 1);
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
        $nextLevel = MatrixLevel::active()
            ->where('required_members', '>', $teamCount)
            ->orderBy('level')
            ->first();

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
                'required_members' => $nextLevelRecursive->required_members,
                'incentive_amount' => $nextLevelRecursive->incentive_amount,
                'remaining_members' => $nextLevelRecursive->required_members - $teamCount,
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

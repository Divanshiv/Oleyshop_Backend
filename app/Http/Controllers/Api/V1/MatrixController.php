<?php

namespace App\Http\Controllers\Api\V1;

use App\CentralLogics\MatrixLogic;
use App\Http\Controllers\Controller;
use App\Model\MatrixIncentiveLog;
use App\Model\MatrixLevel;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MatrixController extends Controller
{
    public function __construct(
        private MatrixLevel $matrixLevel,
        private MatrixIncentiveLog $matrixIncentiveLog
    ) {}

    public function status(Request $request): JsonResponse
    {
        $userId = $request->user()->id;
        $status = MatrixLogic::getMatrixStatus($userId);

        if (empty($status)) {
            return response()->json(['errors' => [['code' => 'matrix', 'message' => 'User not found']]], 404);
        }

        return response()->json($status, 200);
    }

    public function team(Request $request): JsonResponse
    {
        $userId = $request->user()->id;
        $team = MatrixLogic::getTeam($userId);

        return response()->json([
            'total' => count($team),
            'members' => $team,
        ], 200);
    }

    public function tree(Request $request): JsonResponse
    {
        $userId = $request->user()->id;
        $depth = min((int)($request['depth'] ?? 1), 5);

        $tree = MatrixLogic::getMatrixTree($userId, $depth);

        return response()->json($tree, 200);
    }

    public function incentiveHistory(Request $request): JsonResponse
    {
        $userId = $request->user()->id;

        $logs = $this->matrixIncentiveLog
            ->where('user_id', $userId)
            ->orderBy('level', 'asc')
            ->get()
            ->map(function ($log) {
                return [
                    'level' => $log->level,
                    'position_name' => $log->position_name,
                    'amount' => (float)$log->amount,
                    'total_team_members' => $log->total_team_members,
                    'status' => $log->status,
                    'credited_at' => $log->created_at,
                ];
            });

        return response()->json([
            'total_incentive' => (float)$logs->sum('amount'),
            'history' => $logs,
        ], 200);
    }

    public function levels(Request $request): JsonResponse
    {
        $levels = $this->matrixLevel
            ->active()
            ->orderBy('level')
            ->get()
            ->map(function ($level) {
                return [
                    'level' => $level->level,
                    'position_name' => $level->position_name,
                    'required_members' => $level->required_members,
                    'incentive_amount' => (float)$level->incentive_amount,
                ];
            });

        return response()->json($levels, 200);
    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\CentralLogics\Helpers;
use App\CentralLogics\MatrixLogic;
use App\Http\Controllers\Controller;
use App\Model\MatrixIncentiveLog;
use App\Model\MatrixLevel;
use App\Model\MatrixMember;
use App\User;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class MatrixManagementController extends Controller
{
    public function __construct(
        private User $user,
        private MatrixMember $matrixMember,
        private MatrixLevel $matrixLevel,
        private MatrixIncentiveLog $matrixIncentiveLog,
    ) {}

    /**
     * @param Request $request
     * @return Factory|View|Application
     */
    public function index(Request $request): View|Factory|Application
    {
        $perPage = (int) $request->query('per_page', Helpers::getPagination());
        $queryParam = ['per_page' => $perPage];
        $search = $request['search'];

        $customers = $this->user->withCount(['orders'])
            ->with(['matrixMember' => function ($q) {
                $q->withCount(['children']);
            }]);

        if ($request->has('search')) {
            $key = explode(' ', $request['search']);
            $customers = $customers->where(function ($q) use ($key) {
                foreach ($key as $value) {
                    $q->orWhere('f_name', 'like', "%{$value}%")
                        ->orWhere('l_name', 'like', "%{$value}%")
                        ->orWhere('phone', 'like', "%{$value}%")
                        ->orWhere('email', 'like', "%{$value}%");
                }
            });
            $queryParam['search'] = $request->search;
        }

        if ($request->has('matrix_level_filter')) {
            $levelFilter = $request['matrix_level_filter'];
            if ($levelFilter !== '') {
                $customers = $customers->where('matrix_level', $levelFilter);
            }
            $queryParam['matrix_level_filter'] = $request->matrix_level_filter;
        }

        $customers = $customers->latest()->paginate($perPage)->appends($queryParam);

        $levels = $this->matrixLevel->active()->orderBy('level')->get();

        return view('admin-views.customer.matrix-index', compact('customers', 'search', 'perPage', 'queryParam', 'levels'));
    }

    /**
     * @param Request $request
     * @param $id
     * @return Factory|View|Application
     */
    public function tree(Request $request, $id): View|Factory|Application
    {
        $user = $this->user->find($id);
        if (!$user) {
            abort(404);
        }

        $depth = min((int)($request['depth'] ?? 3), 5);
        $tree = MatrixLogic::getMatrixTree($id, $depth);
        $status = MatrixLogic::getMatrixStatus($id);

        $matrixMember = $this->matrixMember->where('user_id', $id)->first();
        $children = [];
        if ($matrixMember) {
            $children = $this->matrixMember->with(['user'])
                ->where('parent_id', $id)
                ->orderBy('position')
                ->get();
        }

        $levels = $this->matrixLevel->active()->orderBy('level')->get();

        return view('admin-views.customer.matrix-tree', compact('user', 'tree', 'depth', 'status', 'children', 'levels', 'matrixMember'));
    }
}

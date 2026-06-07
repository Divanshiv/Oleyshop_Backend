<?php

namespace App\Http\Controllers\Admin;

use App\CentralLogics\CustomerLogic;
use App\CentralLogics\Helpers;
use App\Http\Controllers\Controller;
use App\Model\BusinessSetting;
use App\User;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class MemberController extends Controller
{
    public function __construct(
        private User $user,
        private BusinessSetting $businessSettings,
    ){}

    /**
     * @param Request $request
     * @return Factory|View|Application
     */
    public function list(Request $request): View|Factory|Application
    {
        $perPage = (int) $request->query('per_page', Helpers::getPagination());
        $queryParam = ['per_page' => $perPage];
        $search = $request['search'];

        $customers = $this->user->with(['orders']);

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

        if ($request->has('member_filter')) {
            $memberFilter = $request['member_filter'];
            if ($memberFilter === 'active') {
                $customers = $customers->where('is_member', 1);
            } elseif ($memberFilter === 'inactive') {
                $customers = $customers->where('is_member', 0);
            }
            $queryParam['member_filter'] = $request->member_filter;
        }

        $customers = $customers->latest()->paginate($perPage)->appends($queryParam);

        $memberMilestone = (int) (Helpers::get_business_settings('member_milestone_points') ?: 6500);

        return view('admin-views.customer.member-list', compact('customers', 'search', 'perPage', 'memberMilestone', 'queryParam'));
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function toggleMemberStatus(Request $request, $id): RedirectResponse
    {
        $user = $this->user->find($id);
        if (!$user) {
            Toastr::error(translate('Customer not found!'));
            return back();
        }

        $user->is_member = !$user->is_member;
        $user->save();

        $status = $user->is_member ? 'activated' : 'deactivated';
        Toastr::success(translate("Member status {$status} successfully!"));
        return back();
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function updateMilestone(Request $request): RedirectResponse
    {
        $request->validate([
            'milestone_points' => 'required|integer|min:1'
        ]);

        $setting = $this->businessSettings->where(['key' => 'member_milestone_points'])->first();
        if ($setting) {
            $setting->value = $request['milestone_points'];
            $setting->save();
        } else {
            $this->businessSettings->create([
                'key' => 'member_milestone_points',
                'value' => $request['milestone_points'],
            ]);
        }

        Toastr::success(translate('Member milestone updated successfully!'));
        return back();
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function transferPoints(Request $request): RedirectResponse
    {
        $validator = Validator::make($request->all(), [
            'from_user_id' => 'required|integer|exists:users,id',
            'to_user_id' => 'required|integer|exists:users,id|different:from_user_id',
            'amount' => 'required|integer|min:1',
        ]);

        if ($validator->fails()) {
            Toastr::error(translate('Validation failed: ') . implode(', ', $validator->errors()->all()));
            return back();
        }

        $fromUser = User::find($request->from_user_id);
        if ($fromUser->total_point_value < (int)$request->amount) {
            Toastr::error(translate('Insufficient points. ') . $fromUser->f_name . ' ' . $fromUser->l_name . ' has only ' . number_format($fromUser->total_point_value) . ' points.');
            return back();
        }

        $result = CustomerLogic::transferPoints(
            (int)$request->from_user_id,
            (int)$request->to_user_id,
            (int)$request->amount
        );

        if ($result['success']) {
            Toastr::success($result['message']);
        } else {
            Toastr::error($result['message']);
        }

        return back();
    }
}

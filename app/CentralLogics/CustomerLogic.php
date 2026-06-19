<?php

namespace App\CentralLogics;

use App\Model\BusinessSetting;
use App\Model\MatrixMember;
use App\Models\WalletBonus;
use App\Traits\HelperTrait;
use App\User;
use App\Model\WalletTransaction;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Brian2694\Toastr\Facades\Toastr;


class CustomerLogic{

    use HelperTrait;

    public static function create_wallet_transaction($user_id, float $amount, $transaction_type, $referance)
    {

        if(BusinessSetting::where('key','wallet_status')->first()->value != 1) return false;

        $user = User::find($user_id);
        $current_balance = $user->wallet_balance;

        $wallet_transaction = new WalletTransaction();
        $wallet_transaction->user_id = $user->id;
        $wallet_transaction->transaction_id = Str::random('30');
        $wallet_transaction->reference = $referance;
        $wallet_transaction->transaction_type = $transaction_type;

        $debit = 0.0;
        $credit = 0.0;

        if(in_array($transaction_type, ['add_fund_by_admin','add_fund','referrer', 'add_fund_bonus', 'refund']))
        {
            $credit = $amount;
        }
        else if($transaction_type == 'order_place')
        {
            $debit = $amount;
        }

        $wallet_transaction->credit = $credit;
        $wallet_transaction->debit = $debit;
        $wallet_transaction->balance = $current_balance + $credit - $debit;
        $wallet_transaction->created_at = now();
        $wallet_transaction->updated_at = now();
        $user->wallet_balance = $current_balance + $credit - $debit;


        try{
            DB::beginTransaction();
            $user->save();
            $wallet_transaction->save();
            DB::commit();
            if(in_array($transaction_type, ['order_place','add_fund_by_admin', 'referrer', 'add_fund', 'add_fund_bonus'])) return $wallet_transaction;
            return true;
        }catch(\Exception $ex)
        {
            info($ex);
            DB::rollback();

            return false;
        }
        return false;
    }

    public static function referral_earning_wallet_transaction($user_id, $transaction_type, $referance)
    {
        $user = User::find($referance);
        $current_balance = $user->wallet_balance;

        $debit = 0.0;
        $credit = 0.0;
        $amount = BusinessSetting::where('key','ref_earning_exchange_rate')->first()->value?? 0;
        $credit = $amount;

        $wallet_transaction = new WalletTransaction();
        $wallet_transaction->user_id = $user->id;
        $wallet_transaction->transaction_id = Str::random('30');
        $wallet_transaction->reference = $user_id;
        $wallet_transaction->transaction_type = $transaction_type;
        $wallet_transaction->credit = $credit;
        $wallet_transaction->debit = $debit;
        $wallet_transaction->balance = $current_balance + $credit;
        $wallet_transaction->created_at = now();
        $wallet_transaction->updated_at = now();
        $user->wallet_balance = $current_balance + $credit;

        try{
            DB::beginTransaction();
            $user->save();
            $wallet_transaction->save();
            DB::commit();
            return true;
        }catch(\Exception $ex)
        {
            info($ex);
            DB::rollback();

            return false;
        }
    }

    public static function add_to_wallet($customer_id, float $amount)
    {
        $customer = User::find($customer_id);
        $fcm_token = $customer ? $customer->cm_firebase_token : '';
        $language_code = $customer ? $customer->language_code : 'en';
        $bonus_amount = self::add_to_wallet_bonus($customer_id, $amount);
        $reference = 'add-fund';
        $bonus_value = '';
        $instance = new self();


        $wallet_transaction = self::create_wallet_transaction($customer_id, $amount, 'add_fund', $reference);

        if ($wallet_transaction) {
            if ($bonus_amount > 0){
                $bonus_transaction = self::create_wallet_transaction($customer_id, $bonus_amount, 'add_fund_bonus', 'add-fund-bonus');
                if ($bonus_transaction){
                    $bonus_message = Helpers::order_status_update_message('add_fund_wallet_bonus');

                    if ($language_code != 'en'){
                        $bonus_message = $instance->translate_message($language_code, 'add_fund_wallet_bonus');
                    }
                    $bonus_value = $instance->dynamic_key_replaced_message(message: $bonus_message, type: 'wallet', customer: $customer);
                }
            }

            $message = Helpers::order_status_update_message('add_fund_wallet');

            if ($language_code != 'en'){
                $message = $instance->translate_message($language_code, 'add_fund_wallet');
            }
            $value = $instance->dynamic_key_replaced_message(message: $message, type: 'wallet', customer: $customer);

            try {
                if ($value) {
                    $data = [
                        'title' => translate('wallet'),
                        'description' => $bonus_amount > 0 ? Helpers::set_symbol($amount) . ' ' . $value. ', '. Helpers::set_symbol($bonus_amount). ' '. $bonus_value : Helpers::set_symbol($amount) . ' ' . $value,
                        'order_id' => '',
                        'image' => '',
                        'type' => 'order_status',
                    ];
                    if (isset($fcm_token)) {
                        Helpers::send_push_notif_to_device($fcm_token, $data);
                    }
                }
                return true;
            } catch (\Exception $e) {
                Toastr::warning(translate('Push notification send failed for Customer!'));
            }
        }

        return false;

    }

    /**
     * Process member activation — 6500 point collapse.
     * When a user reaches the milestone, activate membership, deduct the milestone,
     * and credit any excess points to the wallet.
     *
     * @param int $userId
     * @return array ['activated' => bool, 'excess' => float, 'message' => string]
     */
    public static function processMemberActivation(int $userId): array
    {
        $user = User::find($userId);
        if (!$user || $user->is_member) {
            return ['activated' => false, 'excess' => 0, 'message' => $user ? 'Already a member' : 'User not found'];
        }

        $milestone = (int) (BusinessSetting::where('key', 'member_milestone_points')->first()->value ?? 6500);
        if ($user->total_point_value < $milestone) {
            return ['activated' => false, 'excess' => 0, 'message' => 'Milestone not reached'];
        }

        $excess = $user->total_point_value - $milestone;

        DB::transaction(function () use ($user, $milestone, $excess) {
            $user->is_member = true;
            $user->total_point_value = max(0, $excess); // Keep excess as points, never convert to money
            $user->save();
        });

        // After user becomes an active member, check if their parent qualifies for incentives
        $matrixMember = MatrixMember::where('user_id', $userId)->first();
        if ($matrixMember && $matrixMember->parent_id) {
            MatrixLogic::checkAndAwardIncentives($matrixMember->parent_id);
        }

        return [
            'activated' => true,
            'excess' => $excess,
            'message' => $excess > 0
                ? "Member activated, {$excess} excess points remaining in point balance"
                : 'Member activated successfully'
        ];
    }

    /**
     * Transfer points from one user to another.
     *
     * @param int $fromUserId
     * @param int $toUserId
     * @param int $amount
     * @return array ['success' => bool, 'message' => string]
     */
    public static function transferPoints(int $fromUserId, int $toUserId, int $amount): array
    {
        if ($amount <= 0) {
            return ['success' => false, 'message' => 'Transfer amount must be positive'];
        }

        $fromUser = User::find($fromUserId);
        $toUser = User::find($toUserId);

        if (!$fromUser) {
            return ['success' => false, 'message' => 'Sender not found'];
        }
        if (!$toUser) {
            return ['success' => false, 'message' => 'Receiver not found'];
        }
        if ($fromUser->total_point_value < $amount) {
            return ['success' => false, 'message' => 'Insufficient points'];
        }

        DB::transaction(function () use ($fromUser, $toUser, $amount) {
            $fromUser->total_point_value -= $amount;
            $fromUser->save();

            $toUser->total_point_value += $amount;
            $toUser->save();
            // Points stay as points — no wallet money transaction
        });

        // Check if receiver should be activated
        $activation = self::processMemberActivation($toUserId);

        return [
            'success' => true,
            'message' => "{$amount} points transferred successfully" . ($activation['activated'] ? ' and receiver member activated' : ''),
            'receiver_activated' => $activation['activated'],
        ];
    }

    public static function add_to_wallet_bonus($customer_id, float $amount)
    {
        $bonuses = WalletBonus::active()
            ->whereDate('start_date', '<=', now())
            ->whereDate('end_date', '>=', now())
            ->where('minimum_add_amount', '<=', $amount)
            ->get();

        $bonuses = $bonuses->where('minimum_add_amount', $bonuses->max('minimum_add_amount'));

        foreach ($bonuses as $key=>$item) {
            $item->applied_bonus_amount = $item->bonus_type == 'percentage' ? ($amount*$item->bonus_amount)/100 : $item->bonus_amount;

            //max bonus check
            if($item->bonus_type == 'percentage' && $item->applied_bonus_amount > $item->maximum_bonus_amount) {
                $item->applied_bonus_amount = $item->maximum_bonus_amount;
            }
        }

        return $bonuses->max('applied_bonus_amount') ?? 0;
    }

}

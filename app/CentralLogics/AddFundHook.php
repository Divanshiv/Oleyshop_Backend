<?php

use App\CentralLogics\CustomerLogic;
use App\Models\OrderPartialPayment;


if (!function_exists('add_fund_success')) {
    /**
     * @param $data
     * @return void
     */
    function add_fund_success($data): void
    {
        $customer_id = $data['payer_id'];
        $amount = $data['payment_amount'];
        CustomerLogic::add_to_wallet($customer_id, $amount);
    }
}

if (!function_exists('add_fund_fail')) {
    /**
     * @param $data
     * @return void
     */
    function add_fund_fail($data): void
    {
        //
    }
}


if (!function_exists('switch_offline_to_digital_payment_success')) {
    /**
     * @param $data
     * @return void
     */
    function switch_offline_to_digital_payment_success($data): void
    {
        $tran_id = $data['transaction_id'];
        $payment_request_id = $data->id;

        $additional_data = json_decode($data['additional_data'], true);


        if (!is_null($additional_data['order_id'])) {
            $order = \App\Model\Order::find($additional_data['order_id']);
            $order->payment_status = 'paid';
            $order->payment_method = $additional_data['payment_method'];
            $order->order_status = 'confirmed';
            $order->transaction_reference = $tran_id;
            $order->save();

            if ($order->partial_payment->isNotEmpty()) {
                // Update rows where `paid_with` is not 'wallet_payment'
                OrderPartialPayment::create([
                    'order_id' => $order->id,
                    'paid_with' => $additional_data['payment_method'],
                    'paid_amount' => $additional_data['digitally_paid_amount'],
                    'due_amount' => 0,
                ]);
            }

            if ($additional_data['is_partial'] && $order->is_guest == 0){
                // Save wallet payment
                OrderPartialPayment::create([
                    'order_id' => $order->id,
                    'paid_with' => 'wallet_payment',
                    'paid_amount' => $additional_data['wallet_paid_amount'],
                    'due_amount' => $additional_data['digitally_paid_amount'],
                ]);

                // Save remaining payment
                OrderPartialPayment::create([
                    'order_id' => $order->id,
                    'paid_with' => $additional_data['payment_method'],
                    'paid_amount' => $additional_data['digitally_paid_amount'],
                    'due_amount' => 0,
                ]);

                CustomerLogic::create_wallet_transaction($order['user_id'], $additional_data['wallet_paid_amount'], 'order_place', $order['id']);
            }


        }
    }
}

if (!function_exists('switch_offline_to_digital_payment_fail')) {
    /**
     * @param $data
     * @return void
     */
    function switch_offline_to_digital_payment_fail($data): void
    {
        //
    }
}


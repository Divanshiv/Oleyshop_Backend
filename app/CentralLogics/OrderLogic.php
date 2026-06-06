<?php

namespace App\CentralLogics;

use App\Model\Order;
use App\Model\Product;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class OrderLogic
{
    public static function track_order($order_id)
    {
       $order = Order::with(['details',
           'delivery_man' => function ($query) {
               $query->withCount('reviews'); // Count reviews
           },
           'delivery_man.rating', 'partial_payment', 'delivery_address','offline_payment', 'order_image'])->where(['id' => $order_id])->first();
       $order->offline_payment_information = $order->offline_payment ? json_decode($order->offline_payment->payment_info, true): null;
       return $order;
    }

}

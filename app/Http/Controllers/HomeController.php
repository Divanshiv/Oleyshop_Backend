<?php

namespace App\Http\Controllers;

use App\CentralLogics\Helpers;
use App\Model\BusinessSetting;
use App\Model\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        /*$this->middleware('auth');*/
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        return view('home');
    }

    public function orderInvoice($id)
    {
        $order = Order::where(['id' => $id])->first();

        if (!isset($order)) {
            return response()->json([
                'errors' => [['code' => 'order', 'message' => 'Order not found!']]
            ], 404);
        }

        $footer_text = BusinessSetting::where(['key' => 'footer_text'])->first();
        return view('customer-invoice', compact('order', 'footer_text'));
    }
}

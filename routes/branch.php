<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Branch\Auth\LoginController;
use App\Http\Controllers\Branch\DashboardController;
use App\Http\Controllers\Branch\OrderController;
use App\Http\Controllers\Branch\POSController;
use App\Http\Controllers\Branch\ReportController;
use App\Http\Controllers\Branch\SystemController;

/*
|--------------------------------------------------------------------------
| Branch Routes
|--------------------------------------------------------------------------
|
| These routes handle all branch-side functionality including authentication,
| dashboard, POS, orders, reports, and settings. Optimized for Laravel 12.
|
*/

Route::as('branch.')
    ->middleware('maintenance_mode')
    ->group(function () {

        /*
        |--------------------------------------------------------------------------
        | Authentication Routes
        |--------------------------------------------------------------------------
        */
        Route::prefix('auth')
            ->as('auth.')
            ->controller(LoginController::class)
            ->group(function () {
                Route::get('/code/captcha/{tmp}', 'captcha')->name('default-captcha');
                Route::get('login', 'login')->name('login');
                Route::post('login', 'submit');
                Route::get('logout', 'logout')->name('logout');
            });

        /*
        |--------------------------------------------------------------------------
        | Protected Branch Routes
        |--------------------------------------------------------------------------
        */
        Route::middleware(['branch', 'active_branch_check'])->group(function () {

            // Dashboard
            Route::controller(DashboardController::class)->group(function () {
                Route::get('/', 'dashboard')->name('dashboard');
                Route::post('order-stats', 'orderStats')->name('order-stats');
                Route::get('dashboard/order-statistics', 'getOrderStatistics')->name('dashboard.order-statistics');
                Route::get('dashboard/earning-statistics', 'getEarningStatistics')->name('dashboard.earning-statistics');
            });

            // Settings
            Route::controller(SystemController::class)->group(function () {
                Route::get('settings', 'settings')->name('settings');
                Route::post('settings', 'settingsUpdate');
                Route::post('settings-password', 'settingsPasswordUpdate')->name('settings-password');
                Route::get('get-restaurant-data', 'businessData')->name('get-restaurant-data');
            });

            // POS
            Route::prefix('pos')
                ->as('pos.')
                ->controller(POSController::class)
                ->group(function () {
                    Route::get('/', 'index')->name('index');
                    Route::get('quick-view', 'quickView')->name('quick-view');
                    Route::get('quick-view-modal-footer', 'quickViewModalFooter')->name('quick-view-modal-footer');
                    Route::post('variant_price', 'variantPrice')->name('variant_price');
                    Route::post('add-to-cart', 'addToCart')->name('add-to-cart');
                    Route::post('remove-from-cart', 'removeFromCart')->name('remove-from-cart');
                    Route::post('cart-items', 'cartItems')->name('cart_items');
                    Route::post('update-quantity', 'updateQuantity')->name('updateQuantity');
                    Route::post('empty-cart', 'emptyCart')->name('emptyCart');
                    Route::post('tax', 'updateTax')->name('tax');
                    Route::post('update-extra-discount', 'updateExtraDiscount')->name('update-extra-discount');
                    Route::post('delete-extra-discount', 'deleteExtraDiscount')->name('delete-extra-discount');
                    Route::get('customers', 'getCustomers')->name('customers');
                    Route::post('order', 'place_order')->name('order');
                    Route::get('orders', 'order_list')->name('orders');
                    Route::get('order-details/{id}', 'order_details')->name('order-details');
                    Route::get('invoice/{id}', 'generateInvoice');
                    Route::any('store-keys', 'storeKeys')->name('store-keys');
                    Route::any('customer/store', 'newCustomerStore')->name('customer.store');
                    Route::get('orders/export', 'exportOrders')->name('orders.export');
                    Route::post('add-delivery-address', 'addDeliveryInfo')->name('add-delivery-address');
                    Route::post('order_type/store', 'orderTypeStore')->name('order_type.store');
                });

            // Orders
            Route::prefix('orders')
                ->as('orders.')
                ->controller(OrderController::class)
                ->group(function () {
                    Route::get('list/{status}', 'list')->name('list');
                    Route::get('details/{id}', 'details')->name('details');
                    Route::get('status', 'status')->name('status');
                    Route::get('add-delivery-man/{order_id}/{delivery_man_id}', 'addDeliveryman')->name('add-delivery-man');
                    Route::get('payment-status', 'paymentStatus')->name('payment-status');
                    Route::get('generate-invoice/{id}', 'generateInvoice')->name('generate-invoice');
                    Route::post('add-payment-ref-code/{id}', 'addPaymentReferenceCode')->name('add-payment-ref-code');
                    Route::get('export/{status}', 'exportOrders')->name('export');
                    Route::post('update-order-delivery-area/{order_id}', 'updateOrderDeliveryArea')->name('update-order-delivery-area');
                    Route::get('verify-offline-payment/{order_id}/{status}', 'verifyOfflinePayment')->name('verify-offline-payment');
                    Route::get('switch-payment/{id}', 'switchPaymentMethod')->name('switch-payment');
                });

            // Order Actions
            Route::prefix('order')
                ->as('order.')
                ->controller(OrderController::class)
                ->group(function () {
                    Route::get('list/{status}', 'list')->name('list');
                    Route::put('status-update/{id}', 'status')->name('status-update');
                    Route::post('update-shipping/{id}', 'updateShipping')->name('update-shipping');
                    Route::post('update-timeSlot', 'updateTimeSlot')->name('update-timeSlot');
                    Route::post('update-deliveryDate', 'updateDeliveryDate')->name('update-deliveryDate');
                });

            // Reports
            Route::prefix('report')
                ->as('report.')
                ->controller(ReportController::class)
                ->group(function () {
                    Route::get('order-report', 'orderReportIndex')->name('order-report');
                    Route::get('export-order-report', 'exportOrderReport')->name('export-order-report');
                    Route::get('sale-report', 'saleReportIndex')->name('sale-report');
                    Route::get('export-sale-report', 'exportSaleReport')->name('export-sale-report');
                });

            // Offline Payment
            Route::controller(OrderController::class)->group(function () {
                Route::get('verify-offline-payment/quick-view-details', 'offlineQuickViewDetails')->name('offline-modal-view');
                Route::get('verify-offline-payment/{status}', 'offlinePaymentList')->name('verify-offline-payment');
            });
        });
    });

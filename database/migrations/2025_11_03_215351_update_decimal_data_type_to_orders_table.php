<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->decimal('order_amount', 24, 18)->change();
            $table->decimal('coupon_discount_amount', 24, 18)->change();
            $table->decimal('total_tax_amount', 24, 18)->change();
            $table->decimal('delivery_charge', 24, 18)->change();
            $table->decimal('extra_discount', 24, 18)->change();
            $table->decimal('free_delivery_amount', 24, 18)->change();
            $table->decimal('weight_charge_amount', 24, 18)->change();
            $table->decimal('bring_change_amount', 24, 18)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->decimal('order_amount', 8, 2)->change();
            $table->decimal('coupon_discount_amount', 8, 2)->change();
            $table->decimal('total_tax_amount', 8, 2)->change();
            $table->decimal('delivery_charge', 8, 2)->change();
            $table->decimal('extra_discount', 8, 2)->change();
            $table->decimal('free_delivery_amount', 8, 2)->change();
            $table->decimal('weight_charge_amount', 8, 2)->change();
            $table->decimal('bring_change_amount', 8, 2)->change();
        });
    }
};

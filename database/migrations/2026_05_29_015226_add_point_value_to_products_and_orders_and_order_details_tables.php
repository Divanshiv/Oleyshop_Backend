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
        Schema::table('products', function (Blueprint $table) {
            $table->integer('point_value')->default(500)->after('weight');
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->decimal('point_value', 24, 18)->default(0)->after('bring_change_amount');
        });

        Schema::table('order_details', function (Blueprint $table) {
            $table->decimal('point_value', 24, 18)->default(0)->after('vat_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('point_value');
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('point_value');
        });

        Schema::table('order_details', function (Blueprint $table) {
            $table->dropColumn('point_value');
        });
    }
};

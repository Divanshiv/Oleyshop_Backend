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
        // Drop loyalty_transactions table
        Schema::dropIfExists('loyalty_transactions');

        // Drop loyalty_point column from users table
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('loyalty_point');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->double('loyalty_point')->default(0);
        });
    }
};

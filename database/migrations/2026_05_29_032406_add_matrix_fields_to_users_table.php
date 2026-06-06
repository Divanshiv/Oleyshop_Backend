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
        Schema::table('users', function (Blueprint $table) {
            $table->integer('matrix_level')->default(0)->after('referred_by');
            $table->string('matrix_position')->nullable()->after('matrix_level');
            $table->unsignedBigInteger('total_team_members')->default(0)->after('matrix_position');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['matrix_level', 'matrix_position', 'total_team_members']);
        });
    }
};

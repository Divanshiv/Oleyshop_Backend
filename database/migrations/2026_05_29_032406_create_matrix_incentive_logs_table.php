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
        Schema::create('matrix_incentive_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('matrix_level_id');
            $table->integer('level');
            $table->string('position_name');
            $table->decimal('amount', 16, 2);
            $table->unsignedBigInteger('total_team_members');
            $table->string('status')->default('credited');
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('matrix_level_id')->references('id')->on('matrix_levels')->onDelete('cascade');
            $table->unique(['user_id', 'matrix_level_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('matrix_incentive_logs');
    }
};

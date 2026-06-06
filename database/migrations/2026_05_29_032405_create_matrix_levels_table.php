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
        Schema::create('matrix_levels', function (Blueprint $table) {
            $table->id();
            $table->integer('level');
            $table->string('position_name');
            $table->unsignedBigInteger('required_members');
            $table->decimal('incentive_amount', 16, 2);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('matrix_levels');
    }
};

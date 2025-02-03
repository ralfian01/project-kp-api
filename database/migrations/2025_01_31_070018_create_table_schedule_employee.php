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
        // Create employee schedule table
        Schema::create('schedule_employee', function (Blueprint $table) {
            $table->integer('tse_id')->autoIncrement();

            $table->integer('tsc_id')->nullable(false);
            $table->foreign('tsc_id')->references('tsc_id')->on('schedule')
                ->onDelete('cascade')
                ->onUpdate('cascade');

            $table->integer('te_id')->nullable(false);
            $table->foreign('te_id')->references('te_id')->on('employee')
                ->onDelete('cascade')
                ->onUpdate('cascade');

            $table->dateTime('tse_createdAt')->useCurrent();
            $table->dateTime('tse_updatedAt')->useCurrentOnUpdate()->nullable()->default(null);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('schedule_employee');
    }
};

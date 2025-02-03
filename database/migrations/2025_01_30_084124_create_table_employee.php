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
        // Create employee table
        Schema::create('employee', function (Blueprint $table) {
            $table->integer('te_id')->autoIncrement();
            $table->string('te_name', 200)->nullable(false);

            $table->boolean('te_statusActive')->default(true)->nullable(false);

            $table->dateTime('te_createdAt')->useCurrent();
            $table->dateTime('te_updatedAt')->useCurrentOnUpdate()->nullable()->default(null);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employee');
    }
};

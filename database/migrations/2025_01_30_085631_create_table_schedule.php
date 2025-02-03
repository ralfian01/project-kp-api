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
        // Create schedule table
        Schema::create('schedule', function (Blueprint $table) {
            $table->integer('tsc_id')->autoIncrement();
            $table->string('tsc_shiftCode', 2)->nullable(false);
            $table->string('tsc_productionDate', 15)->nullable(false);
            $table->string('tsc_expiredDate', 15)->nullable(false);

            $table->integer('tpr_id')->nullable(false)->comment('product id');
            $table->foreign('tpr_id')->references('tpr_id')->on('product')
                ->onDelete('no action')
                ->onUpdate('no action');

            $table->integer('tm_id')->nullable(false)->comment('machine id');
            $table->foreign('tm_id')->references('tm_id')->on('machine')
                ->onDelete('no action')
                ->onUpdate('no action');

            $table->dateTime('tsc_createdAt')->useCurrent();
            $table->dateTime('tsc_updatedAt')->useCurrentOnUpdate()->nullable()->default(null);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('table_schedule');
    }
};

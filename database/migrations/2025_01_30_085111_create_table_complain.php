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
        // Create complain table
        Schema::create('complain', function (Blueprint $table) {
            $table->integer('tc_id')->autoIncrement();
            $table->string('tc_number', 10)->nullable(false);
            $table->string('tc_expiredCode', 18)->nullable(false);
            $table->string('tc_category', 100)->nullable();
            $table->string('tc_description', 500)->nullable();
            $table->string('tc_receiveMedia', 20)->nullable();
            $table->string('tc_date', 12)->nullable();
            $table->string('tc_productStatus', 25)->nullable();
            $table->string('tc_evidencePath')->nullable();

            $table->integer('tpr_id')->nullable(false);
            $table->foreign('tpr_id')->references('tpr_id')->on('product')
                ->onDelete('no action')
                ->onUpdate('no action');

            $table->dateTime('tc_createdAt')->useCurrent();
            $table->dateTime('tc_updatedAt')->useCurrentOnUpdate()->nullable()->default(null);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('complain');
    }
};

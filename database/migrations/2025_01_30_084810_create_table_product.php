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
        // Create product table
        Schema::create('product', function (Blueprint $table) {
            $table->integer('tpr_id')->autoIncrement();
            $table->string('tpr_name', 100)->nullable(false);
            $table->integer('tpr_weight',)->nullable(false)->comment('in grams');
            $table->integer('tpr_expired')->nullable(false)->comment('in days');
            $table->string('tpr_imagePath')->nullable();

            $table->dateTime('tpr_createdAt')->useCurrent();
            $table->dateTime('tpr_updatedAt')->useCurrentOnUpdate()->nullable()->default(null);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product');
    }
};

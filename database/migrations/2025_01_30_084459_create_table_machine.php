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
        // Create machine table
        Schema::create('machine', function (Blueprint $table) {
            $table->integer('tm_id')->autoIncrement();
            $table->string('tm_code', 10)->nullable(false)->unique();
            $table->string('tm_name', 100)->nullable(false);

            $table->dateTime('tm_createdAt')->useCurrent();
            $table->dateTime('tm_updatedAt')->useCurrentOnUpdate()->nullable()->default(null);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('machine');
    }
};

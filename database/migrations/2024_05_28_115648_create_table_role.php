<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Create role table
        Schema::create('role', function (Blueprint $table) {
            $table->integer('tr_id')->autoIncrement();
            $table->string('tr_code', 30)->nullable(false)->unique();
            $table->string('tr_name', 100);
            $table->dateTime('tr_createdAt')->useCurrent();
            $table->dateTime('tr_updatedAt')->useCurrentOnUpdate()->nullable()->default(null);
        });

        // Create role privilege table
        Schema::create('role__privilege', function (Blueprint $table) {
            $table->integer('trp_id')->autoIncrement();
            // # Relation to table Role
            $table->integer('tr_id')->nullable(false);
            $table->foreign('tr_id')->references('tr_id')->on('role')
                ->onDelete('cascade')
                ->onUpdate('no action');

            // # Relation to table Privilege
            $table->integer('tp_id')->nullable(false);
            $table->foreign('tp_id')->references('tp_id')->on('privilege')
                ->onDelete('cascade')
                ->onUpdate('no action');
            $table->unique(['tr_id', 'tp_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('role');
        Schema::dropIfExists('role__privilege');
    }
};

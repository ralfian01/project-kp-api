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
        // Create account table
        Schema::create('account', function (Blueprint $table) {
            $table->integer('ta_id')->autoIncrement();
            $table->string('ta_uuid', 50)->nullable(false);
            $table->string('ta_username', 100)->nullable(false);
            $table->string('ta_password', 100)->nullable(true);
            // # Relation to table Role
            $table->integer('tr_id')->nullable(false);
            $table->foreign('tr_id')->references('tr_id')->on('role')
                ->onDelete('no action')
                ->onUpdate('no action');

            $table->boolean('ta_deletable')->default(true)->nullable(false);
            $table->boolean('ta_statusActive')->default(false)->nullable(false);
            $table->boolean('ta_statusDelete')->default(false)->nullable(false);

            $table->dateTime('ta_createdAt')->useCurrent();
            $table->dateTime('ta_updatedAt')->useCurrentOnUpdate()->nullable()->default(null);
        });

        // Create account privilege table
        Schema::create('account__privilege', function (Blueprint $table) {
            $table->integer('tap_id')->autoIncrement();
            // # Relation to table Account
            $table->integer('ta_id')->nullable(false);
            $table->foreign('ta_id')->references('ta_id')->on('account')
                ->onDelete('cascade')
                ->onUpdate('no action');

            // # Relation to table Privilege
            $table->integer('tp_id')->nullable(false);
            $table->foreign('tp_id')->references('tp_id')->on('privilege')
                ->onDelete('cascade')
                ->onUpdate('no action');
        });

        // Create account metadata table
        Schema::create('account__meta', function (Blueprint $table) {
            $table->integer('tam_id')->autoIncrement();
            $table->string('tam_code', 30)->nullable(false);
            $table->string('tam_value', 100)->nullable(true)->default(null);

            // # Relation to table Account
            $table->integer('ta_id')->nullable(false);
            $table->foreign('ta_id')->references('ta_id')->on('account')
                ->onDelete('cascade')
                ->onUpdate('no action');

            $table->dateTime('ta_createdAt')->useCurrent();
            $table->dateTime('ta_expiredAt')->nullable(true);
        });

        // Create account table view
        // Here
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('account');
        Schema::dropIfExists('account__privilege');
        Schema::dropIfExists('account__meta');
    }
};

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
        // Update foreign keys in boarding_houses table to cascade on delete
        Schema::table('boarding_houses', function (Blueprint $table) {
            $table->dropForeign(['city_id']);
            $table->dropForeign(['category_id']);

            $table->foreign('city_id')->references('id')->on('cities')->cascadeOnDelete();
            $table->foreign('category_id')->references('id')->on('categories')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
        Schema::table('boarding_houses', function (Blueprint $table) {
            $table->dropForeign(['city_id']);
            $table->dropForeign(['category_id']);

            $table->foreign('city_id')->references('id')->on('cities');
            $table->foreign('category_id')->references('id')->on('categories');
        });
    }
};

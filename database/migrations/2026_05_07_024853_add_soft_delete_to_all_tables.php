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

        // Tabel: city
        Schema::table('cities', function (Blueprint $table) {
            //
            $table->softDeletes();
        });

        // Tabel: categories
        Schema::table('categories', function (Blueprint $table) {
            //
            $table->softDeletes();
        });

        // Tabel: boarding_houses
        Schema::table('boarding_houses', function (Blueprint $table) {
            //
            $table->softDeletes();
        });


        // Tabel: rooms
        Schema::table('rooms', function (Blueprint $table) {
            //
            $table->softDeletes();
        });

        // Tabel: room_images
        Schema::table('room_images', function (Blueprint $table) {
            //
            $table->softDeletes();
        });

        // Tabel: bonuses
        Schema::table('bonuses', function (Blueprint $table) {
            //
            $table->softDeletes();
        });

        // Tabel: testimonials
        Schema::table('testimonials', function (Blueprint $table) {
            //
            $table->softDeletes();
        });

        // Tabel: transactions
        Schema::table('transactions', function (Blueprint $table) {
            //
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Tabel: city
        Schema::table('cities', function (Blueprint $table) {
            //
            $table->dropSoftDeletes();
        });

        // Tabel: categories
        Schema::table('categories', function (Blueprint $table) {
            //
            $table->dropSoftDeletes();
        });

        // Tabel: boarding_houses
        Schema::table('boarding_houses', function (Blueprint $table) {
            //
            $table->dropSoftDeletes();
        });


        // Tabel: rooms
        Schema::table('rooms', function (Blueprint $table) {
            //
            $table->dropSoftDeletes();
        });

        // Tabel: room_images
        Schema::table('room_images', function (Blueprint $table) {
            //
            $table->dropSoftDeletes();
        });

        // Tabel: bonuses
        Schema::table('bonuses', function (Blueprint $table) {
            //
            $table->dropSoftDeletes();
        });

        // Tabel: testimonials
        Schema::table('testimonials', function (Blueprint $table) {
            //
            $table->dropSoftDeletes();
        });

        // Tabel: transactions
        Schema::table('transactions', function (Blueprint $table) {
            //
            $table->dropSoftDeletes();
        });
    }
};

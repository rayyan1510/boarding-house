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
        //
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropForeign(['boarding_house_id']);
            $table->dropForeign(['room_id']);

            $table->foreign('boarding_house_id')->references('id')->on('boarding_houses')->cascadeOnDelete();
            $table->foreign('room_id')->references('id')->on('rooms')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropForeign(['boarding_house_id']);
            $table->dropForeign(['room_id']);

            $table->foreign('boarding_house_id')->references('id')->on('boarding_houses');
            $table->foreign('room_id')->references('id')->on('rooms');
        });
    }
};

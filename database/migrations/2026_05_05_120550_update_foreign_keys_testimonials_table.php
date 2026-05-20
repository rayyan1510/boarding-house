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
        Schema::table('testimonials', function (Blueprint $table) {
            $table->dropForeign(['boarding_house_id']);

            $table->foreign('boarding_house_id')->references('id')->on('boarding_houses')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
        Schema::table('testimonials', function (Blueprint $table) {
            $table->dropForeign(['boarding_house_id']);
            $table->foreign('boarding_house_id')->references('id')->on('boarding_houses');
        });
    }
};

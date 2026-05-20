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
        Schema::table('rooms', function (Blueprint $table) {
            $table->dropForeign(['boarding_house_id']);

            $table->foreign('boarding_house_id')->references('id')->on('boarding_houses')->cascadeOnDelete();

            // Tambahan field capacity
            $table->integer('capacity')->default(1)->after('square_feet');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
        Schema::table('rooms', function (Blueprint $table) {
            $table->dropForeign(['boarding_house_id']);

            $table->foreign('boarding_house_id')->references('id')->on('boarding_houses');

            // Rollback field capacity
            $table->dropColumn('capacity');
        });
    }
};

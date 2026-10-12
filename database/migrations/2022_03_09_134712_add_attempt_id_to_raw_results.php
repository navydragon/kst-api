<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAttemptIdToRawResults extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('raw_results', function (Blueprint $table) {
            $table->unsignedBigInteger('attempt_id')->nullable();
            $table->foreign('attempt_id')->references('id')->on('attempts');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('raw_results', function (Blueprint $table) {
            //
        });
    }
}

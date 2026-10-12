<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRawResultsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('raw_results', function (Blueprint $table) {
            $table->id();
            $table->string('user_name')->nullable();
            $table->integer('earned_score');
            $table->integer('passing_score');
            $table->integer('max_score');
            $table->integer('used_time');
            $table->integer('full_used_time')->nullable();
            $table->longtext('result');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('raw_results');
    }
}

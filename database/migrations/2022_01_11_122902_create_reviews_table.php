<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReviewsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            $table->string('reviews_id', 250);
            $table->unsignedInteger('application_id');
            $table->string('version', 25);
            $table->string('url', 255);
            $table->string('author', 255);
            $table->string('title', 255);
            $table->string('description', 3000);
            $table->string('country', 10);
            $table->string('score', 10);
            $table->dateTimeTz('reviewed_at');
            $table->unsignedInteger('votes');
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
        Schema::dropIfExists('reviews');
    }
}

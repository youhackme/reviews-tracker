<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateApplicationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('applications', function (Blueprint $table) {
            $table->id();
            $table->string('applications_id');
            $table->string('name', 250);
            $table->json('screenshots');
            $table->string('icon', 250);
            $table->string('developer_url', 250);
            $table->json('languages');
            $table->unsignedInteger('reviews');
            $table->string('score', 10);
            $table->string('url', 250);
            $table->dateTimeTz('released_at');
            $table->unsignedInteger('developer_id');
            $table->string('genre', 75);
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
        Schema::dropIfExists('applications');
    }
}

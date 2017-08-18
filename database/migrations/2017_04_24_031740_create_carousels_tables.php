<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateCarouselsTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('carousels', function (Blueprint $table) {
            $table->increments('id');

            $table->string('key', 255)->nullable()->index();
            $table->string('name', 255)->nullable();
            $table->tinyInteger('for_logged_in')->default(2);
            $table->integer('display_days')->nullable();
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();

            $table->string('status')->default('active')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('carouselimages', function (Blueprint $table) {
            $table->increments('id');

            $table->unsignedInteger('parent_id')->nullable()->index();
            $table->integer('lft')->nullable()->index();
            $table->integer('rgt')->nullable()->index();
            $table->integer('depth')->nullable();

            $table->string('name', 255)->nullable();
            $table->string('path', 255)->nullable();
            $table->string('url', 255)->nullable();
            $table->boolean('use_html')->default(false)->nullable();
            $table->longText('html')->nullable();
            $table->float('displaying_time')->nullable();
            $table->integer('clicks')->default(0)->nullable();

            $table->string('status')->default('active')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('carousel_carouselimage', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('carousel_id');
            $table->unsignedInteger('carouselimage_id');
            $table->nullableTimestamps();

            $table->unique(['carousel_id', 'carouselimage_id']);

            $table->foreign('carousel_id')
                ->references('id')->on('carousels')
                ->onDelete('cascade')
                ->onUpdate('cascade');
            $table->foreign('carouselimage_id')
                ->references('id')->on('carouselimages')
                ->onDelete('cascade')
                ->onUpdate('cascade');
        });

        Schema::create('carousel_user', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('carousel_id');
            $table->unsignedInteger('user_id');
            $table->nullableTimestamps();

            $table->unique(['carousel_id', 'user_id']);

            $table->foreign('carousel_id')
                ->references('id')->on('carousels')
                ->onDelete('cascade')
                ->onUpdate('cascade');
            $table->foreign('user_id')
                ->references('id')->on('users')
                ->onDelete('cascade')
                ->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('carousel_user');
        Schema::drop('carousel_carouselimage');
        Schema::drop('carouselimages');
        Schema::drop('carousels');
    }
}

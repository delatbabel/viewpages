<?php
/**
 * Class CreateVppagesJoinTables
 */

use Illuminate\Database\Migrations\Migration;

/**
 * Class CreateVppagesJoinTables
 *
 * Migration script that creates the vptemplates table.
 */
class CreateVppagesJoinTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        /** @var \Illuminate\Database\Schema\Blueprint $table */
        Schema::create('vptemplate_website', function ($table) {
            $table->increments('id');
            $table->integer('vptemplate_id')->unsigned();
            $table->integer('website_id')->unsigned()->nullable();

            $table->foreign('vptemplate_id')
                ->references('id')->on('vptemplates')
                ->onDelete('cascade')
                ->onUpdate('cascade');
            $table->foreign('website_id')
                ->references('id')->on('websites')
                ->onDelete('set null')
                ->onUpdate('cascade');
        });

        /** @var \Illuminate\Database\Schema\Blueprint $table */
        Schema::create('vppage_website', function ($table) {
            $table->increments('id');
            $table->integer('vppage_id')->unsigned();
            $table->integer('website_id')->unsigned()->nullable();

            $table->foreign('vppage_id')
                ->references('id')->on('vppages')
                ->onDelete('cascade')
                ->onUpdate('cascade');
            $table->foreign('website_id')
                ->references('id')->on('websites')
                ->onDelete('set null')
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
        Schema::drop('vppage_website');
        Schema::drop('vptemplate_website');
    }
}

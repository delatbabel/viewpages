<?php
/**
 * Class CreateVpagesJoinTables
 */

use Illuminate\Database\Migrations\Migration;

/**
 * Class CreateVpagesJoinTables
 *
 * Migration script that creates the vptemplates table.
 */
class CreateVpagesJoinTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        /** @var \Illuminate\Database\Schema\Blueprint $table */
        Schema::create('vpage_website', function ($table) {
            $table->increments('id');
            $table->integer('vpage_id')->unsigned();
            $table->integer('website_id')->unsigned()->nullable();

            $table->foreign('vpage_id')
                ->references('id')->on('vpages')
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
        Schema::drop('vpage_website');
    }
}

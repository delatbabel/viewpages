<?php
/**
 * Class CreateVobjectsTable
 */

use Illuminate\Database\Migrations\Migration;

/**
 * Class CreateVobjectsTable
 *
 * Migration script that creates the vobjects table.
 */
class CreateVobjectsTable extends Migration
{
    /**@var string */
    protected $tableName;

    public function __construct()
    {
        $this->tableName = 'vobjects';
    }

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        /** @var \Illuminate\Database\Schema\Blueprint $table */
        Schema::create($this->tableName, function ($table) {
            $table->increments('id');
            $table->integer('website_id')->unsigned()->nullable();
            $table->integer('category_id')->unsigned()->nullable();
            $table->string('objectkey', 255)->default('')->index();
            $table->string('name', 255)->default('');
            $table->string('description', 255)->nullable();
            $table->longText('content')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('website_id')
                ->references('id')->on('websites')
                ->onDelete('set null')
                ->onUpdate('cascade');

            $table->foreign('category_id')
                ->references('id')->on('categories')
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
        Schema::drop($this->tableName);
    }
}

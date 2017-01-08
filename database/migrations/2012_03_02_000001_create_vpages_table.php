<?php
/**
 * Class CreateVpagesTable
 */

use Illuminate\Database\Migrations\Migration;

/**
 * Class CreateVpagesTable
 *
 * Migration script that creates the vpages table.
 */
class CreateVpagesTable extends Migration
{
    /**@var string */
    protected $tableName;

    public function __construct()
    {
        $this->tableName = 'vpages';
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
            $table->integer('category_id')->unsigned()->nullable();
            $table->string('namespace', 255)->default('');
            $table->string('pagekey', 255)->default('');
            $table->string('url', 255)->default('')->index();
            $table->string('name', 255)->default('');
            $table->string('description', 255)->nullable();
            $table->string('pagetype', 20)->default('blade');
            $table->boolean('is_secure')->default(false);
            $table->longText('content')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['namespace', 'pagekey']);

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

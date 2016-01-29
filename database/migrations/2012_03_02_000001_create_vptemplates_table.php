<?php
/**
 * Class CreateVptemplatesTable
 */

use Illuminate\Database\Migrations\Migration;

/**
 * Class CreateVptemplatesTable
 *
 * Migration script that creates the vptemplates table.
 */
class CreateVptemplatesTable extends Migration
{
    /**@var string */
    protected $tableName;

    public function __construct()
    {
        $this->tableName = 'vptemplates';
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
            $table->string('key', 255)->default('')->index();
            $table->string('name', 255)->default('');
            $table->string('description', 255)->nullable();
            $table->boolean('is_default')->default(false);
            $table->longText('content')->nullable();
            $table->timestamps();
            $table->softDeletes();
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

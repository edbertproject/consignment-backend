<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * Class CreateDistrictsTable.
 */
class CreateDistrictsTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('districts', function(Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('raja_ongkir_id');
            $table->relation('city_id', 'cities', false);
            $table->unsignedBigInteger('raja_ongkir_city_id');
            $table->string('name');

            $table->baseStamps();
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('districts');
	}
}

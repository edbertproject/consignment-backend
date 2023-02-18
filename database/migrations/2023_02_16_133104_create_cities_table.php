<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * Class CreateCitiesTable.
 */
class CreateCitiesTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('cities', function(Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('raja_ongkir_id');
            $table->relation('province_id', 'provinces', false);
            $table->unsignedBigInteger('raja_ongkir_province_id');
            $table->string('type');
            $table->string('name');
            $table->string('postal_code');

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
		Schema::drop('cities');
	}
}

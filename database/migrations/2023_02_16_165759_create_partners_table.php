<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * Class CreatePartnersTable.
 */
class CreatePartnersTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('partners', function(Blueprint $table) {
            $table->id();
            $table->relation('user_id', 'users', false);
            $table->text('full_address');
            $table->string('postal_code');
            $table->relation('province_id', 'provinces', false);
            $table->relation('city_id', 'cities', false);
            $table->relation('district_id', 'districts', false);

            $table->boolean('is_approve');

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
		Schema::drop('partners');
	}
}

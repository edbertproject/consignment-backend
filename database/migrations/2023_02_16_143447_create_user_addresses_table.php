<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * Class CreateUserAddressesTable.
 */
class CreateUserAddressesTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('user_addresses', function(Blueprint $table) {
            $table->id();
            $table->relation('user_id', 'users', false);
            $table->string('label');
            $table->string('receiver_name');
            $table->string('phone_number');
            $table->text('full_address');
            $table->string('postal_code');
            $table->relation('province_id', 'provinces', false);
            $table->relation('city_id', 'cities', false);
            $table->relation('district_id', 'districts', false);
            $table->text('note')->nullable();
            $table->boolean('is_primary');

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
		Schema::drop('user_addresses');
	}
}

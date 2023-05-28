<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * Class CreateProductParticipantsTable.
 */
class CreateProductParticipantsTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('product_participants', function(Blueprint $table) {
            $table->id();

            $table->relation('product_id', 'products', false);
            $table->relation('user_id', 'users', false);
            $table->baseStamps(false,false);
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('product_participants');
	}
}

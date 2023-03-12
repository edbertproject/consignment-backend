<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * Class CreateCartsTable.
 */
class CreateCartsTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('carts', function(Blueprint $table) {
            $table->id();
            $table->relation('user_id', 'users', false);
            $table->relation('product_id', 'products', false);
            $table->unsignedBigInteger('quantity')->default(1);
            $table->boolean('is_available')->default(1);
            $table->baseStamps(false);
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('carts');
	}
}

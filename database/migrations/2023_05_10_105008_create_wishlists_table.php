<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * Class CreateWishlistsTable.
 */
class CreateWishlistsTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('wishlists', function(Blueprint $table) {
            $table->id();

            $table->relation('product_id', 'products', false);
            $table->relation('user_id', 'users', false);
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
		Schema::drop('wishlists');
	}
}

<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * Class CreateProductBidsTable.
 */
class CreateProductBidsTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('product_bids', function(Blueprint $table) {
            $table->id();

            $table->relation('product_id', 'products', false);
            $table->unsignedBigInteger('amount');
            $table->relation('user_id','users',false);
            $table->timestamp('date_time');

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
		Schema::drop('product_bids');
	}
}

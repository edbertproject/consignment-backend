<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * Class CreateOrderStatusesTable.
 */
class CreateOrderStatusesTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('order_statuses', function(Blueprint $table) {
            $table->id();
            $table->uuidRelation('order_id', 'orders', false);
            $table->string('status');
            $table->string('type');
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
		Schema::drop('order_statuses');
	}
}

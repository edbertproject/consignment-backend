<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * Class CreateOrdersTable.
 */
class CreateOrdersTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('orders', function(Blueprint $table) {
            $table->uuid('id')->primary();
            $table->date('date');
            $table->string('number')->unique();
            $table->uuidRelation('invoice_id', 'invoices', false);
            $table->relation('user_id', 'users', false);
            $table->relation('user_address_id', 'user_addresses', false);
            $table->relation('product_id', 'products', false);
            $table->relation('partner_id', 'partners', true);
            $table->unsignedBigInteger('quantity')->default(0);
            $table->unsignedDouble('price');
            $table->string('status');
            $table->text('notes')->nullable();
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
		Schema::drop('orders');
	}
}

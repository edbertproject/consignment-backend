<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * Class CreateInvoicesTable.
 */
class CreateInvoicesTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('invoices', function(Blueprint $table) {
            $table->uuid('id')->primary();
            $table->relation('user_id', 'users', false);
            $table->date('date');
            $table->string('number')->unique();
            $table->relation('payment_method_id', 'payment_methods', false);
            $table->string('payment_number')->nullable();
            $table->unsignedDouble('subtotal');
            $table->unsignedDouble('tax_amount')->default(0);
            $table->unsignedDouble('admin_fee')->default(0);
            $table->unsignedDouble('platform_fee')->default(0);
            $table->unsignedDouble('grand_total');
            $table->string('xendit_key')->nullable();
            $table->string('status');
            $table->timestamp('expires_at')->nullable();
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
		Schema::drop('invoices');
	}
}

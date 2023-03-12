<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * Class CreatePaymentMethodInstructionsTable.
 */
class CreatePaymentMethodInstructionsTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('payment_method_instructions', function(Blueprint $table) {
            $table->id();
            $table->relation('payment_method_id', 'payment_methods', false);
            $table->string('title');
            $table->text('instructions');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('payment_method_instructions');
	}
}

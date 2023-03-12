<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * Class CreatePaymentMethodsTable.
 */
class CreatePaymentMethodsTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('payment_methods', function(Blueprint $table) {
            $table->id();
            $table->string('type');
            $table->string('code');
            $table->string('name');
            $table->string('description')->nullable();
            $table->string('xendit_code');
            $table->boolean('is_enabled')->default(1);
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
		Schema::drop('payment_methods');
	}
}

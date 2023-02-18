<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * Class CreateProductsTable.
 */
class CreateProductsTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('products', function(Blueprint $table) {
            $table->id();
            $table->relation('product_category_id', 'product_categories', false);
            $table->string('type');
            $table->string('name');
            $table->unsignedBigInteger('price')->nullable();

            // bid only`
            $table->unsignedBigInteger('start_price')->nullable();
            $table->unsignedBigInteger('multiplied_price')->nullable();
            $table->unsignedBigInteger('desired_price')->nullable();

            $table->timestamp('start_date')->nullable();
            $table->unsignedDouble('weight')->default(1);
            $table->unsignedBigInteger('quantity')->default(1);
            $table->unsignedDouble('long_dimension')->nullable();
            $table->unsignedDouble('wide_dimension')->nullable();
            $table->unsignedDouble('high_dimension')->nullable();
            $table->string('condition');
            $table->string('warranty');
            $table->text('description')->nullable();

            $table->text('cancel_reason')->nullable();

            $table->boolean('is_active')->default(false);
            $table->boolean('is_approve')->default(false);
            $table->boolean('is_close')->default(false);
            $table->boolean('is_cancel')->default(false);

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
		Schema::drop('products');
	}
}

<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Utils\Constants;

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

            // bid only
            $table->unsignedBigInteger('start_price')->nullable();
            $table->unsignedBigInteger('multiplied_price')->nullable();
            $table->unsignedBigInteger('desired_price')->nullable();

            $table->timestamp('start_date');
            $table->timestamp('end_date')->nullable();
            $table->unsignedDouble('weight')->default(0);
            $table->unsignedBigInteger('quantity')->default(1);
            $table->unsignedDouble('long_dimension')->nullable();
            $table->unsignedDouble('wide_dimension')->nullable();
            $table->unsignedDouble('high_dimension')->nullable();
            $table->string('condition');
            $table->string('warranty');
            $table->text('description')->nullable();

            $table->text('cancel_reason')->nullable();
            $table->string('status')->default(Constants::PRODUCT_STATUS_WAITING_APPROVAL);

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

<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * Class CreateApiExternalLogsTable.
 */
class CreateApiExternalLogsTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('api_external_logs', function(Blueprint $table) {
            $table->id();

            $table->string('vendor');
            $table->text('url');
            $table->text('request_header')->nullable();
            $table->text('request_body');
            $table->text('response');

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
		Schema::drop('api_external_logs');
	}
}

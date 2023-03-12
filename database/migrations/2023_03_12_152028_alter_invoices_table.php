<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->string('courier_code')->nullable()->after('payment_number');
            $table->string('courier_service')->nullable()->after('courier_code');
            $table->string('courier_esd')->nullable()->after('courier_service');
            $table->unsignedDouble('courier_cost')->default(0)->after('platform_fee');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropColumn([
                'courier_code',
                'courier_service',
                'courier_esd',
                'courier_cost',
            ]);
        });
    }
};

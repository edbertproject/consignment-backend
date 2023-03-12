<?php

namespace App\Providers;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\ServiceProvider;

class DatabaseMacroServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        Blueprint::macro('baseStamps', function(bool $withSoftDelete = true, bool $withTimestamps = true, bool $withUserstamps = true) {
            if ($withTimestamps) $this->timestamps();
            if ($withSoftDelete) $this->softDeletes();
            if ($withUserstamps) {
                $this->unsignedBigInteger('created_by')->nullable()->index();
                $this->unsignedBigInteger('updated_by')->nullable()->index();
                $this->unsignedBigInteger('deleted_by')->nullable()->index();
            }
        });

        Blueprint::macro('relation', function($column, $table, $nullable = true, $foreign='id') {
            $this->unsignedBigInteger($column)->nullable($nullable)->index();
            $this->foreign($column)->on($table)->references($foreign)->onUpdate('cascade');
        });

        Blueprint::macro('uuidRelation', function($column, $table, $nullable = true) {
            $this->uuid($column)->nullable($nullable)->index();
            $this->foreign($column)->on($table)->references('id')->onUpdate('cascade');
        });
    }
}

<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

//prevents errors with string field in the database
use Illuminate\Support\Facades\Schema;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //prevents length error from occuring
        Schema::defaultStringlength(191);
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}

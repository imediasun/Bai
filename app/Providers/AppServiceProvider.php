<?php

namespace App\Providers;

use App\Http\Helpers\SypexGeo\GeoHelper;
use App\SeoRecord;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
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
        Schema::defaultStringLength(191);

        $credit_period = \CommonHelper::getCreditTerms();
        view()->share('credit_period', $credit_period);

//        $geo = new GeoHelper();
//        $city = $geo->getCityFromSession();
//        $shared = [
//            'city' => $city,
////            'city' => $city,
//        ];
//        view()->share('shared', $shared);
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

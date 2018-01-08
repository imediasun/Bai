<?php

namespace App\Providers;

use App\Http\Helpers\SypexGeo\GeoHelper;
use App\SeoRecord;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
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

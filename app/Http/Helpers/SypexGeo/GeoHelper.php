<?php

namespace App\Http\Helpers\SypexGeo;

use App\City;
use \Illuminate\Http\Request;

class GeoHelper extends \SxGeo
{

    function __construct($db_file = 'SxGeoCity.dat', $type = SXGEO_FILE)
    {
        $db_file = dirname(__FILE__).'/'.$db_file;
        parent::__construct($db_file, $type);
    }

    public function getCityFromSession()
    {
        $request = request();
        $locale = app()->getLocale();
        $data = session()->get('geo');
        if (!is_array($data)) {
            $user_ip = $request->ip();
            $geo_city = $this->getCity($user_ip);
            if($geo_city){

                $city = $geo_city['city']['name_en'];
                $city_entity = City::where('alt_name_ru', $city)->first();
                if ($city_entity){
                    $result = [
                        'id' => $city_entity->id,
                        'regionId' => $city_entity->region->id,
                        'name' => $city_entity->name_{$locale},
                        'altName' => $city_entity->alt_name_{$locale},
                    ];
                    session(['geo' => $result]);

                    return $result;

                }

            }

        }

        return $data;

    }

    public static function setCityToSession($cityName)
    {
        $locale = app()->getLocale();
        $city = City::where('alt_name_ru', $cityName)->first();
        $result = [
            'id' => $city->id,
            'regionId' => $city->region->id,
            'name' => $city->name_{$locale},
            'altName' => $city->alt_name_{$locale},
        ];
        session(['geo' => $result]);
    }

}
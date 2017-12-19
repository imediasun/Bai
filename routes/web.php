<?php

Route::get('/', function () {
    return view('welcome');
});

//$pas = \TCG\Voyager\Models\PracticeArea::join('practice_area_categories', 'practice_area_categories.id', '=', 'practice_areas.pa_category_id')
//    ->select([
//        'practice_areas.slug_en as slug_en',
//        'practice_areas.slug_es as slug_es',
//
//    ])->get();

/*get all cities*/
/*get all banks*/

Route::group(['prefix' => 'kredity'], function () {
    Route::get('/', 'CreditController@index')->name('credit_index');
    Route::get('{cityOrBank}', 'CreditController@indexCityOrBank')->name('credit_index_city_or_bank');
    Route::get('{bank}/{credit}', 'CreditController@creditPage')->name('credit_page');
});

Route::group(['prefix' => 'ajax'], function (){
//    Route::post('');
});


Route::group(['prefix' => 'admin'], function () {
    Voyager::routes();
});

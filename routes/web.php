<?php

Route::get('/', 'MainController@index')->name('index');

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

    ///kredity/almaty/
    ///kredity/kassa-nova/
    ///kredity/kassa-nova/almaty/
    ///kredity/kredity-bez-podtverzhdeniya-dohoda/
    ///kredity/kredity-bez-podtverzhdeniya-dohoda/almaty/
    Route::get('{cityOrBankOrFastFilter}/{city?}', 'CreditController@filterByUrl')->name('filterByUrl');


    Route::get('{cityOrBank}', 'CreditController@indexCityOrBank')->name('credit_index_city_or_bank');
    Route::get('{bank}/{credit}', 'CreditController@creditPage')->name('credit_page');
});

Route::group(['prefix' => 'ajax'], function (){
    Route::get('credit-calc', 'AjaxController@credit_calc')->name('ajax.credit_calc');
//    Route::post('{product}/compare/toggleCompare/{id}', 'AjaxController@compare')->name('ajax.compare.add');
    Route::post('compare/toggleCompare/', 'AjaxController@compare')->name('ajax.compare.add');
    Route::get('{product}/compare/compareList', 'AjaxController@compareList')->name('ajax.compare.list');
});

Route::group(['prefix' => 'test'], function (){
    Route::get('fees', 'TestController@fee_test')->name('test.fee_test');
});


Route::group(['prefix' => 'admin'], function () {

//    Route::post('credits/update/{id}?', 'Admin\\CreditController@update')->name('tst');


    Route::group(['prefix' => 'ajax'], function (){
        Route::post('get-prop-block', 'Admin\\AjaxController@getProductProps')->name('add_product_props');
        Route::post('del-prop-block', 'Admin\\AjaxController@delProductProps')->name('del_product_props');

        Route::post('get-fee-block', 'Admin\\AjaxController@getProductFees')->name('add_product_fees');
        Route::post('del-fee-block', 'Admin\\AjaxController@delProductFees')->name('del_product_fees');

        Route::post('translit', 'Admin\\AjaxController@translit')->name('translit');

        Route::post('get-custom-prop-block', 'Admin\\AjaxController@getCustomProp')->name('add_custom_prop_block');
        Route::post('del-custom-prop-block', 'Admin\\AjaxController@delCustomProp')->name('del_custom_prop_block');




    });

    Voyager::routes();
});

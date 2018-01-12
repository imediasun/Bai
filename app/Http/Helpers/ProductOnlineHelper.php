<?php

use \Illuminate\Http\Request;
use \App\City;

class ProductOnlineHelper
{
    public function step1(Request $request, $product)
    {
        $all = $request->all();
        $cities = City::all();

        $credit = [
            'amount' => $all['amount'],
            'time' => $all['time'],
            'term_human' => $all['term_human'],
            'currency' => $all['currency'],
            'term' => $all['term'],
            'overpay' => $all['overpay'],
            'currency_symbol' => $all['currency_symbol'],
            'bank_logo' => $all['logo'],
        ];

        $result = [
            'cities' => $cities,
            'credit' => $credit,
        ];


        $output = view('common.modal.credit.step_1', $result)->render();

        return response()->json(array('success' => true, 'html' => $output, 'cr' => $credit));


    }

}
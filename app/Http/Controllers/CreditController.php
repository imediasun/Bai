<?php

namespace App\Http\Controllers;

use App\Bank;
use App\City;
use App\Credit;
use App\Currency;
use App\FastFilter;
use Illuminate\Http\Request;

class CreditController extends Controller
{
    public function index()
    {
        $credits = Credit::all();

        return view('credit.index');
    }

    public function creditsByBank()
    {

    }

    public function creditsByCity()
    {

    }

    public function creditsByFF()
    {

    }

    public function creditsByFF_city()
    {

    }

    public function filterByUrl($object, $city = null)
    {
        $result = [];
        $where = [];
        //если параметр $city пустой, значит, это либо быстрый фильтр либо банк
        if($city == null){
            
            // проверяем, банк ли это
            $bank = Bank::where('alt_name_'.$this->locale, $object)
                ->where('is_main', true)
                ->first();

            // если банк не найден - то это ff
            if(!$bank){
                $fastFilter = FastFilter::where('alt_name_'.$this->locale, $object)
                    ->where('product_type', 'credit')
                    ->first();
                if($fastFilter){
                    $fastFilterProps = $fastFilter->fastFilterProps;
                    foreach ($fastFilterProps as $item) {
                        $where[] =
                            [
                                $item['product_field'],
                                $item['sign'],
                                $item['value']
                            ];
                    }

                    $credits = Credit::where($where)->get();

                    $result = [
                        'credits' => $credits,
                    ];

                    return view('credit.index', $result);
                }


                
                //todo: find credits by ff
                $credits = [];

            }else{
                $credits = $bank->credits;

            }
            

            $result = [
                'credits' => $credits,
            ];

            return view('credit.index', $result);
        }
        else{
            $city = City::where('alt_name_'.$this->locale, $city)->first();

            if($city){


                $bank = Bank::where('alt_name_'.$this->locale, $object)
                    ->where('is_main', true)
                    ->first();

                if ($bank){

                    $bank_is_presented = $bank->bank_to_city;

                    if($bank_is_presented){
                        $credits = $bank->credits;

                        $result = [
                            'credits' => $credits,
                        ];

                        return view('credit.index', $result);
                    }
                }
                else{
                    $fastFilter = FastFilter::where('alt_name_'.$this->locale, $object)
                        ->where('product_type', 'credit')
                        ->first();

                    if($fastFilter){
                        $fastFilterProps = $fastFilter->fastFilterProps;
                        foreach ($fastFilterProps as $item) {
                            $where[] = [
                                    $item['product_field'],
                                    $item['sign'],
                                    $item['value']
                                ];
                        }

                        $where[] = [
                            'bank.city_id',
                            '=',
                            $city->id
                        ];

                        $credits = Credit::where($where)
                            ->join('banks', 'credits.bank_id', '=', 'banks.id')
                            ->join('credit_props', 'credits.id', '=', 'credit_props.credit_id')
                            ->join('fees', function ($join) {
                                $join->on('credits.id', '=', 'fees.product_id')
                                    ->where('fees.product_type', '=', 'credit');
                            })
                            ->leftJoin('fee_types', 'fees.fee_type_id', '=', 'fee_types.id')
                            ->leftJoin('fee_values', 'fees.fee_value_id', '=', 'fee_values.id')
                            ->groupBy('credits.id')
                            ->get(['credits.*', 'credit_props.*', 'fees.*']);


                        $result = [
                            'credits' => $credits,
                        ];

                        return view('credit.index', $result);
                    }
                }

            }


        }

        abort(404);

    }


    public function creditPage($bank, $credit)
    {
        $bank = Bank::where('alt_name_'.$this->locale, $bank)
            ->where('is_main', true)
            ->first();
        if($bank != null){
            $credit = $bank->credits()->where('alt_name_'.$this->locale, $credit)->first();

            if ($credit != null){

                // выбираем нужные нам валюты
                $currencies_codes = ['usd', 'eur', 'rub', 'gbp', 'chf', 'jpy', 'cny'];
                $currencies = Currency::whereIn('code', $currencies_codes)->get();

                // выбираем филиалы банка
                $branches = Bank::where('alt_name_'.$this->locale, $bank)
                    ->where('is_main', false)
                    ->get();

                $atms = $bank->atms;

                $result = [
                    'branches' => $branches,
                    'currencies' => $currencies,
                    'credit' => $credit,
                    'atms' => $atms,
                ];

                return view('credit.page', $result);
            }
        }

        abort(404);
    }


}

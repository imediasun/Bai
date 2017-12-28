<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Currency extends Model
{
    public function getExchangeCurrencies()
    {
        $currencies = $this->all();

        $needCurrencies = array('usd', 'eur', 'rub', 'gbp', 'chf', 'jpy', 'cny');
        $data = array();

        foreach ($needCurrencies as $needCurrency) {
            foreach ($currencies as $currency) {
                if (strtolower($currency->code) == $needCurrency) {
                    $data[strtolower($currency->code)] = $currency;
                }
            }
        }

        return $data;
    }
}

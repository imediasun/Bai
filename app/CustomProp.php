<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CustomProp extends Model
{
    protected $fillable = [
        'credit_id',
        'deposit_id',
        'auto_credit_id',
        'credit_card_id',
        'debit_card_id',
        'mortgage_id',
        'loan_id',
        'bank_id',
        'name_ru',
        'alt_name_ru',
        'value_ru',
        'alt_value_ru',
        'comment_ru',
        'name_kz',
        'alt_name_kz',
        'value_kz',
        'alt_value_kz',
        'comment_kz',

    ];

    public function credit()
    {
        return $this->belongsTo('App\Credit');
    }

    //todo: для других продуктов аналогично
}

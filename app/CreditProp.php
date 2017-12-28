<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CreditProp extends Model
{
    protected $fillable = [
        'credit_id',
        'min_amount',
        'max_amount',
        'min_period',
        'max_period',
        'percent_rate',
        'currency',
        'income_confirmation',
        'gesv',
        'repayment_structure',
        'credit_security',
    ];

    public function fees()
    {
        return $this->hasMany('App\CreditPropFee');
    }
}

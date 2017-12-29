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
        'age',
        'income_project',
        'client_type',

    ];

    public function fees()
    {
        return $this->hasMany('App\CreditPropFee');
    }

    public function setMinAmountAttribute($value)
    {
        if($value == ''){
            $this->attributes['min_amount'] = null;
        }
        else{
            $this->attributes['min_amount'] = $value;
        }
    }

    public function setMaxAmountAttribute($value)
    {
        $cleared = $value;
        $cleared = str_replace([','], '.', $cleared);
        $cleared = str_replace(['%', 'от', ' '], '', $cleared);
        $cleared = str_replace([' '], '', $cleared);
        $cleared = filter_var($cleared, FILTER_SANITIZE_NUMBER_INT);


        if($cleared == ''){
            $this->attributes['max_amount'] = null;
        }
        else{
            $this->attributes['max_amount'] = $cleared;
        }
    }

    public function setMinPeriodAttribute($value)
    {
        if($value == ''){
            $this->attributes['min_period'] = null;
        }
        else{
            $this->attributes['min_period'] = $value;
        }
    }

    public function setPercentRateAttribute($value)
    {
        if($value == ''){
            $this->attributes['percent_rate'] = null;
        }
        else{
            $this->attributes['percent_rate'] = $value;
        }
    }
}

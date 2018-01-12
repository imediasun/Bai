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
        'amount_comment',
        'currency_comment',
        'period_comment',
        'percent_rate_comment',
        'age_comment',
        'income_confirmation_comment',
        'credit_security_comment',
        'repayment_structure_comment',
        'gesv',
        'gesv_comment',

    ];

    public function credit()
    {
        return $this->belongsTo('App\Credit');
    }

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

    public static function transform_currency($item)
    {
        $arr = [
            'kzt' => '₸',
            'usd' => '$',
            'eur' => '€',
            '' => '₸',
        ];

        if(isset($arr[$item])){
            return $arr[$item];
        }

        return null;
    }

    public static function transform_security($item)
    {
        $arr = [
            '' => 'без залога и поручительства',
            'none' => 'без залога и поручительства',
            'without-security' => 'без залога и поручительства',
            'guarantor' => 'поручитель',
            'deposit' => 'залог - депозит',
            'immovables_current' => 'залог - имеющееся недвижимость',
            'immovables_bying' => 'залог - приобретемая недвижимость',
            'auto_current' => 'залог - имеющееся авто',
            'auto_buying' => 'залог - приобретаемое авто',
            'money' => 'залог - денежные средства',
        ];

        if(isset($arr[$item])){
            return $arr[$item];
        }

        return null;
    }

    public static function transform_credit_history($item)
    {
        $arr = [
            'none' => 'не важно',
            'positive' => 'положительная кредитная история',
            'negative' => 'отрицательная кредитная история',
        ];
        if(isset($arr[$item])){
            return $arr[$item];
        }

        return null;
    }

    public static function transform_credit_formalization($item)
    {
        $arr = [
            'none' => 'не важно',
            'online' => 'онлайн заявка',
            'office' => 'в отделении банка',
            'both' => 'в отделений банка и онлайн заявка',
        ];
        if(isset($arr[$item])){
            return $arr[$item];
        }

        return null;
    }
}

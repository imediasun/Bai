<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Credit extends Model
{
    protected $fillable = [
        'bank_id',
        'insurance',
        'insurance_input',
        'name_ru',
        'name_kz',
        'alt_name_ru',
        'alt_name_kz',
        'short_description_ru',
        'short_description_kz',
        'description_ru',
        'description_kz',
        'online_url',
        'promo',
        'is_approved',
        'breadcrumbs_ru',
        'breadcrumbs_kz',
        'meta_title_ru',
        'meta_title_kz',
        'changed_by',
        'created_by',
        'sort_order',
        'h1_ru',
        'h1_kz',
        'meta_description_ru',
        'meta_description_kz',
        'minimum_income',
        'occupational_life',
        'occupational_current',
        'have_constant_income',
        'have_mobile_phone',
        'have_work_phone',
        'have_early_repayment',
        'have_prolongation',
        'have_citizenship',
        'method_of_repayment_ru',
        'method_of_repayment_kz',
        'docs_ru',
        'docs_kz',
        'other_claims_ru',
        'other_claims_kz',
        'debtor_category',
        'credit_goal',
        'receive_mode',
        'registration',
        'time_for_consideration',
        'credit_history',
        'credit_formalization',
        'gesv',
        'gesv_comment',
        'time_for_consideration_comment',
        'have_early_repayment_comment',
        'occupational_life_comment',
        'minimum_income_comment',
    ];

    public function bank()
    {
        return $this->belongsTo('App\Bank');
    }

    public function props()
    {
        return $this->hasMany('App\CreditProp');
    }

    public function custom_props()
    {
        return $this->hasMany('App\CustomProp');
    }

    public static function transform_credit_goal($item)
    {
        $arr = [
            'none' => 'не важно',
            'any' => 'любая',
            'emergency_needs' => 'неотложные нужды',
            'just_money' => 'просто деньги',
            'goods' => 'товары',
            'business' => 'бизнес',
            'refinancing' => 'рефинансирование',
            'medication' => 'лечение',
            'education' => 'образование',
            'traveling' => 'путешествие',
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

//    public function scopeppm($query, $id)
//    {
//        return $query->where('');
//    }

//    public function ppm($id)
//    {
//        return 123;
//    }
}

<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Credit extends Model
{
    protected $fillable = [
        'bank_id',
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
    ];

    public function props()
    {
        return $this->hasMany('App\CreditProp');
    }

    public function custom_props()
    {
        return $this->hasMany('App\CustomProp');
    }
}

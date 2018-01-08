<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CreditPropFee extends Model
{
    protected $fillable = [
        'credit_id',
        'credit_prop_id',
        'review',
        'organization',
        'card_account_enrolment',
        'monetisation',
        'service',
        'granting',
        'review_input',
        'organization_input',
        'card_account_enrolment_input',
        'monetisation_input',
        'service_input',
        'granting_input',
        'created_by',
        'changed_by',
    ];

    public function credit()
    {
        return $this->belongsTo('App\Credit');
    }
}

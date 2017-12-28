<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CreditPropFee extends Model
{
    protected $fillable = [
        'credit_id',
        'credit_prop_id',
        'fee_type_id',
        'fee_value_id',
        'input',
    ];

    public function credit()
    {
        return $this->belongsTo('App\Credit');
    }
}

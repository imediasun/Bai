<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class FastFilter extends Model
{
    protected $fillable = [
        'sort_order',
        'is_approved',
        'product_type',
        'name_ru',
        'name_kz',
        'alt_name_ru',
        'alt_name_kz',
    ];

    public function props()
    {
        return $this->hasMany('App\FastFilterProp');
    }
}

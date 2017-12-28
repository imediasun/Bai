<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class FeeType extends Model
{
    public function fee_values()
    {
        return $this->hasMany('App\FeeValue');
    }
}

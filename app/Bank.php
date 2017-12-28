<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Bank extends Model
{
    public function parents()
    {
        return $this->belongsTo('App\Bank', 'parent_id', 'id');
    }
}

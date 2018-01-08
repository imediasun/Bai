<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    public function scopegetReviews($query)
    {
        return $query->where('parent_id', null)->where('is_approved', true);
    }
}

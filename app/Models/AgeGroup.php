<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AgeGroup extends Model
{
    use HasFactory;

    public function races()
    {
        return $this->belongsToMany(Race::class, 'race_age_group');
    }
}

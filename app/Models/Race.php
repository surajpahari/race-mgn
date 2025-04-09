<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Race extends Model
{
    use HasFactory;

    public function ageGroups()
    {
        return $this->belongsToMany(AgeGroup::class, 'race_age_group');
    }
}

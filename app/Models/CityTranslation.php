<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CityTranslation extends Model
{
    use HasFactory;

    protected $fillable = [
        'city_id',
        'local',
        'name'
    ];

    protected $casts = [
        'city_id' => 'integer'
    ];

    public function city()
    {
        return $this->belongsTo(City::class);
    }
}

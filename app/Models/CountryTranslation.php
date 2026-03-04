<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CountryTranslation extends Model
{
    use HasFactory;

    protected $fillable = [
        'country_id',
        'local',
        'name'
    ];

    protected $casts = [
        'country_id' => 'integer'
    ];

    public function country()
    {
        return $this->belongsTo(Country::class);
    }
}

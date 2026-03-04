<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GovernorateTranslation extends Model
{
    use HasFactory;

    protected $fillable = [
        'governorate_id',
        'local',
        'name'
    ];

    protected $casts = [
        'governorate_id' => 'integer'
    ];

    public function governorate()
    {
        return $this->belongsTo(Governorate::class);
    }
}

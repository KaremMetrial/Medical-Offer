<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProviderTranslation extends Model
{
    use HasFactory;

    protected $fillable = [
        'provider_id',
        'local',
        'name',
        'title',
        'description'
    ];

    protected $casts = [
        'provider_id' => 'integer'
    ];

    public function provider()
    {
        return $this->belongsTo(Provider::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NationalityTranslation extends Model
{
    use HasFactory;

    protected $fillable = [
        'nationality_id',
        'name',
        'local',
    ];

    protected $casts = [
        'nationality_id' => 'integer',
        'local' => 'string',
    ];

    public function nationality()
    {
        return $this->belongsTo(Nationality::class);
    }
}

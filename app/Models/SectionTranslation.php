<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SectionTranslation extends Model
{
    use HasFactory;

    protected $fillable = [
        'section_id',
        'local',
        'name'
    ];

    public function section()
    {
        return $this->belongsTo(Section::class);
    }
}

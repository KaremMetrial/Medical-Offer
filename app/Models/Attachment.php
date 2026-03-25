<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attachment extends Model
{
    use HasFactory;

    protected $fillable = [
        'path',
        'type',
        'file_type'
    ];

    public function attachable()
    {
        return $this->morphTo();
    }

    public function getUrlAttribute(): ?string
    {
        return $this->path ? asset('storage/' . $this->path) : null;
    }
}

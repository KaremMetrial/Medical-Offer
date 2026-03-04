<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProviderCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'provider_id',
        'category_id'
    ];

    protected $casts = [
        'provider_id' => 'integer',
        'category_id' => 'integer'
    ];

    public function provider()
    {
        return $this->belongsTo(Provider::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}

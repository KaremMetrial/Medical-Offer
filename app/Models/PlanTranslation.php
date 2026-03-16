<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PlanTranslation extends Model
{
    use HasFactory;

    protected $fillable = [
        'plan_id',
        'local',
        'name',
        'label',
        'feature'
    ];

    protected $casts = [
        'plan_id' => 'integer',
        'feature' => 'array'
    ];

    public function plan()
    {
        return $this->belongsTo(MemberPlan::class);
    }
}

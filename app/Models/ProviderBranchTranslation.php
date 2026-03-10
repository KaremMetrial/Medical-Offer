<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProviderBranchTranslation extends Model
{
    use HasFactory;

    protected $fillable = [
        'provider_branch_id',
        'local',
        'name'
    ];

    protected $casts = [
        'provider_branch_id' => 'integer'
    ];

    public function providerBranch()
    {
        return $this->belongsTo(ProviderBranch::class);
    }
}

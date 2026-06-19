<?php

namespace Ajangsupardi\PostcodeId\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Regency extends Model
{
    protected $guarded = [];

    public function getTable(): string
    {
        return (config('postcode.table_prefix') ?? '').'regencies';
    }

    public function province(): BelongsTo
    {
        return $this->belongsTo(config('postcode.models.province'), 'province_id');
    }

    public function districts(): HasMany
    {
        return $this->hasMany(config('postcode.models.district'), 'regency_id');
    }
}

<?php

namespace Ajangsupardi\PostcodeId\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class District extends Model
{
    protected $guarded = [];

    public function getTable(): string
    {
        return (config('postcode.table_prefix') ?? '').'districts';
    }

    public function regency(): BelongsTo
    {
        return $this->belongsTo(config('postcode.models.regency'), 'regency_id');
    }

    public function villages(): HasMany
    {
        return $this->hasMany(config('postcode.models.village'), 'district_id');
    }
}

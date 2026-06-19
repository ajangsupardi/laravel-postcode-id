<?php

namespace Ajangsupardi\PostcodeId\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Village extends Model
{
    protected $guarded = [];

    public function getTable(): string
    {
        return (config('postcode.table_prefix') ?? '').'villages';
    }

    public function district(): BelongsTo
    {
        return $this->belongsTo(config('postcode.models.district'), 'district_id');
    }

    public function scopePostalCode($query, string $code)
    {
        return $query->where('postal_code', $code);
    }
}

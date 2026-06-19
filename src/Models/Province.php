<?php

namespace Ajangsupardi\PostcodeId\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Province extends Model
{
    protected $guarded = [];

    public function getTable(): string
    {
        return (config('postcode.table_prefix') ?? '').'provinces';
    }

    public function regencies(): HasMany
    {
        return $this->hasMany(config('postcode.models.regency'), 'province_id');
    }
}

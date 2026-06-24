<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class InterestCategory extends Model
{
    protected $keyType = 'string';

    public $incrementing = false;

    protected $fillable = [
        'id', 'label', 'icon',
    ];

    public function interests(): HasMany
    {
        return $this->hasMany(Interest::class, 'category_id');
    }
}

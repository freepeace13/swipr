<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Interest extends Model
{
    protected $keyType = 'string';

    public $incrementing = false;

    protected $fillable = [
        'id', 'category_id', 'label', 'icon',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(InterestCategory::class, 'category_id');
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(
            related: User::class,
            table: 'user_interests',
            foreignPivotKey: 'interest_id',
            relatedPivotKey: 'user_id'
        )->withPivot('weight');
    }
}

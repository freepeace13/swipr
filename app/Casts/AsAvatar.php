<?php

namespace App\Casts;

use App\Support\Avatar;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;

class AsAvatar implements CastsAttributes
{
    public function get(Model $model, string $key, mixed $value, array $attributes): Avatar
    {
        return new Avatar($value);
    }

    public function set(Model $model, string $key, mixed $value, array $attributes): ?string
    {
        if ($value instanceof Avatar) {
            return $value->path();
        }

        return $value;
    }
}

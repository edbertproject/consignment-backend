<?php

namespace App\Utils\Traits;

trait WithAbility
{
    public array $appends = [
        'can_update',
        'can_delete',
    ];

    public function getCanUpdateAttribute(): bool
    {
        return true;
    }

    public function getCanDeleteAttribute(): bool
    {
        return true;
    }
}

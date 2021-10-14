<?php

namespace Binarcode\LaravelDeveloper\Nova\Fields;

use Carbon\Carbon;
use Exception;
use Laravel\Nova\Fields\Badge;

class ExpirationDateBadge extends Badge
{
    public function resolveBadgeClasses()
    {
        try {
            return parent::resolveBadgeClasses();
        } catch (Exception $e) {
            return Carbon::parse($this->value)->lt(now())
                ? $this->types['danger']
                : $this->types['success'];
        }
    }
}

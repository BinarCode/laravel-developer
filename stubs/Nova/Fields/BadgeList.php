<?php

namespace Nova\Fields;

use Exception;

class BadgeList extends \Laravel\Nova\Fields\Badge
{
    protected string $defaultType = 'info';

    public function resolveBadgeClasses()
    {
        try {
            return parent::resolveBadgeClasses();
        } catch (Exception) {
            return $this->types[$this->defaultType];
        }
    }

    public function defaultType(string $type = 'info')
    {
        $this->defaultType = $type;

        return $this;
    }
}

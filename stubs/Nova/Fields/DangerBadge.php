<?php

namespace Nova\Fields;

use Exception;

class DangerBadge extends Badge
{
    public function __construct($name, $attribute = null, callable $resolveCallback = null)
    {
        parent::__construct($name, $attribute, $resolveCallback);

        $this->defaultType = 'danger';
    }

    public function resolveBadgeClasses()
    {
        try {
            return parent::resolveBadgeClasses();
        } catch (Exception $e) {
            return $this->types[$this->defaultType];
        }
    }
}

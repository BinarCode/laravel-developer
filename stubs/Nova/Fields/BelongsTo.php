<?php

namespace Nova\Fields;

use Laravel\Nova\Fields\BelongsTo as NovaBelongsTo;
use Laravel\Nova\Fields\ID;
use function optional;
use function request;

class BelongsTo extends NovaBelongsTo
{
    public function resolve($resource, $attribute = null)
    {
        $value = null;

        if ($resource->relationLoaded($this->attribute)) {
            $value = $resource->getRelation($this->attribute);
        }

        if (! $value) {
            $value = $resource->{$this->attribute}()->withoutGlobalScopes()->getResults();
        }

        if ($value) {
            $resource = new $this->resourceClass($value);

            // This line was very time consuming.
            $this->belongsToId = $value->getKey() ?? optional(ID::forResource($resource))->value;

            $this->value = $this->formatDisplayValue($resource);

            $this->viewable = $this->viewable
                && $resource->authorizedToView(request());
        }
    }
}

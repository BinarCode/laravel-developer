<?php

namespace Binarcode\LaravelDeveloper\Tests\Mock;

use JsonSerializable;

class PayloadMock implements JsonSerializable
{
    public function jsonSerialize()
    {
        return [
            'message' => 'wew',
        ];
    }
}

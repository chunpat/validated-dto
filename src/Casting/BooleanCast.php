<?php

declare(strict_types=1);

namespace Chunpat\ValidatedDto\Casting;

class BooleanCast implements Castable
{
    public function cast(string $property, $value): bool
    {
        if (is_numeric($value)) {
            return $value > 0;
        }

        if (is_string($value)) {
            return filter_var($value, FILTER_VALIDATE_BOOLEAN);
        }

        return (bool) $value;
    }
}

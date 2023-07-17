<?php

declare(strict_types=1);

namespace Chunpat\ValidatedDto\Casting;

class ArrayCast implements Castable
{
    public function cast(string $property, $value): array
    {
        if (is_string($value)) {
            $jsonDecoded = json_decode($value, true);

            return is_array($jsonDecoded) ? $jsonDecoded : [$value];
        }

        return is_array($value) ? $value : [$value];
    }
}

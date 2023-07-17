<?php

declare(strict_types=1);

namespace Chunpat\ValidatedDto\Casting;

use Chunpat\ValidatedDto\Exceptions\CastException;

class FloatCast implements Castable
{
    /**
     * @throws CastException
     */
    public function cast(string $property, $value): float
    {
        if (! is_numeric($value)) {
            throw new CastException($property);
        }

        return (float) $value;
    }
}

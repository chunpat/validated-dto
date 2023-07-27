<?php

declare(strict_types=1);

namespace Chunpat\ValidatedDTO\Casting;

use Chunpat\ValidatedDTO\Exceptions\CastException;

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

<?php

declare(strict_types=1);

namespace Chunpat\ValidatedDto\Casting;

use Chunpat\ValidatedDto\Exceptions\CastException;

class IntegerCast implements Castable
{
    /**
     * @throws CastException
     */
    public function cast(string $property, $value): int
    {
        if (! is_numeric($value)) {
            throw new CastException($property);
        }

        return (int) $value;
    }
}

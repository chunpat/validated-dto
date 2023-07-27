<?php

declare(strict_types=1);

namespace Chunpat\ValidatedDTO\Casting;

use Chunpat\ValidatedDTO\Exceptions\CastException;

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

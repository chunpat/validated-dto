<?php

declare(strict_types=1);

namespace Chunpat\ValidatedDTO\Casting;

use Chunpat\ValidatedDTO\Exceptions\CastException;

class ObjectCast implements Castable
{
    /**
     * @throws CastException
     */
    public function cast(string $property, $value): object
    {
        if (is_string($value)) {
            $value = json_decode($value, true);
        }

        if (! is_array($value)) {
            throw new CastException($property);
        }

        return (object) $value;
    }
}

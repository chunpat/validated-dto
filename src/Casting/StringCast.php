<?php

declare(strict_types=1);

namespace Chunpat\ValidatedDto\Casting;

use Throwable;
use Chunpat\ValidatedDto\Exceptions\CastException;

class StringCast implements Castable
{
    /**
     * @throws CastException
     */
    public function cast(string $property, $value): string
    {
        try {
            return (string) $value;
        } catch (Throwable $ex) {
            throw new CastException($property);
        }
    }
}

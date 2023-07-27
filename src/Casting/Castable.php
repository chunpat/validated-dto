<?php

declare(strict_types=1);

namespace Chunpat\ValidatedDTO\Casting;

interface Castable
{
    /**
     * Casts the given value.
     */
    public function cast(string $property, $value);
}

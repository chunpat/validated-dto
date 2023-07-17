<?php

declare(strict_types=1);

namespace Chunpat\ValidatedDto\Casting;

interface Castable
{
    /**
     * Casts the given value.
     */
    public function cast(string $property, $value);
}

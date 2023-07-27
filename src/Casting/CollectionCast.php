<?php

declare(strict_types=1);

namespace Chunpat\ValidatedDTO\Casting;

use Illuminate\Support\Collection;

class CollectionCast implements Castable
{
    private $type = null;

    public function __construct(?Castable $type = null)
    {
        $this->type = $type;
    }

    public function cast(string $property, $value): Collection
    {
        $arrayCast = new ArrayCast();
        $value = $arrayCast->cast($property, $value);

        return Collection::make($value)
            ->when($this->type, fn ($collection, $castable) => $collection->map(fn ($item) => $castable->cast($property, $item)));
    }
}

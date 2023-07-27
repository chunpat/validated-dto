<?php

declare(strict_types=1);

namespace Chunpat\ValidatedDTO\Casting;

use Carbon\CarbonImmutable;
use Throwable;
use Chunpat\ValidatedDTO\Exceptions\CastException;

class CarbonImmutableCast implements Castable
{
    private $timezone = null;
    private $format = null;

    public function __construct(
        ?string $timezone = null,
        ?string $format = null
    ) {
        $this->format = $format;
        $this->timezone = $timezone;
    }

    /**
     * @throws CastException
     */
    public function cast(string $property, $value): CarbonImmutable
    {
        try {
            return is_null($this->format)
                ? CarbonImmutable::parse($value, $this->timezone)
                : CarbonImmutable::createFromFormat($this->format, $value, $this->timezone);
        } catch (Throwable $ex) {
            throw new CastException($property);
        }
    }
}

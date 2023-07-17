<?php

declare(strict_types=1);

namespace Chunpat\ValidatedDto\Casting;

use Carbon\Carbon;
use Throwable;
use Chunpat\ValidatedDto\Exceptions\CastException;

class CarbonCast implements Castable
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
    public function cast(string $property, $value): Carbon
    {
        try {
            return is_null($this->format)
                ? Carbon::parse($value, $this->timezone)
                : Carbon::createFromFormat($this->format, $value, $this->timezone);
        } catch (Throwable $ex) {
            throw new CastException($property);
        }
    }
}

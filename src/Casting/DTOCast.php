<?php

declare(strict_types=1);

namespace Chunpat\ValidatedDto\Casting;

use Illuminate\Validation\ValidationException;
use Throwable;
use Chunpat\ValidatedDto\Exceptions\CastException;
use Chunpat\ValidatedDto\Exceptions\CastTargetException;
use Chunpat\ValidatedDto\SimpleDTO;

class DTOCast implements Castable
{
    private $dtoClass;

    public function __construct(string $dtoClass)
    {
        $this->dtoClass = $dtoClass;
    }

    /**
     * @throws CastException|CastTargetException|ValidationException
     */
    public function cast(string $property, $value): SimpleDTO
    {
        if (is_string($value)) {
            $value = json_decode($value, true);
        }

        if (! is_array($value)) {
            throw new CastException($property);
        }

        try {
            $dto = new $this->dtoClass($value);
        } catch (ValidationException $exception) {
            throw $exception;
        } catch (Throwable) {
            throw new CastException($property);
        }

        if (! ($dto instanceof SimpleDTO)) {
            throw new CastTargetException($property);
        }

        return $dto;
    }
}

<?php

declare(strict_types=1);

namespace Chunpat\ValidatedDTO;

//use Illuminate\Console\Command;
use Illuminate\Contracts\Console\Application;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
//use Illuminate\Database\Eloquent\Model;
//use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Facade;
use Illuminate\Validation\ValidationException;
use Chunpat\ValidatedDTO\Casting\ArrayCast;
use Chunpat\ValidatedDTO\Casting\Castable;
use Chunpat\ValidatedDTO\Exceptions\CastTargetException;
use Chunpat\ValidatedDTO\Exceptions\InvalidJsonException;
use Chunpat\ValidatedDTO\Exceptions\MissingCastTypeException;

abstract class SimpleDTO implements CastsAttributes
{
    protected $data = [];

    protected $validatedData = [];

    protected $requireCasting = false;

    protected $validator;

    /**
     * @throws ValidationException|MissingCastTypeException|CastTargetException
     */
    public function __construct(?array $data = null)
    {
        if (is_null($data)) {
            return;
        }

        $this->data = $this->buildDataForValidation($data);

        $this->initConfig();

        $this->isValidData()
            ? $this->passedValidation()
            : $this->failedValidation();
    }

    public function __set(string $name, $value): void
    {
        $this->{$name} = $value;
    }

    public function __get(string $name)
    {
        return $this->{$name} ?? null;
    }

    /**
     * Defines the default values for the properties of the DTO.
     */
    abstract protected function defaults(): array;

    /**
     * Defines the type casting for the properties of the DTO.
     */
    abstract protected function casts(): array;

    /**
     * Creates a DTO instance from a valid JSON string.
     *
     * @return $this
     *
     * @throws InvalidJsonException|ValidationException|MissingCastTypeException|CastTargetException
     */
    public static function fromJson(string $json): self
    {
        $jsonDecoded = json_decode($json, true);
        if (! is_array($jsonDecoded)) {
            throw new InvalidJsonException();
        }

        return new static($jsonDecoded);
    }

    /**
     * Creates a DTO instance from Array.
     *
     * @return $this
     *
     * @throws CastTargetException|MissingCastTypeException
     */
    public static function fromArray(array $array): self
    {
        return new static($array);
    }

    /**
     * Creates a DTO instance from a Request.
     *
     * @return $this
     *
     * @throws ValidationException|MissingCastTypeException|CastTargetException
     */
//    public static function fromRequest(Request $request): self
//    {
//        return new static($request->all());
//    }

    /**
     * Creates a DTO instance from the given model.
     *
     * @return $this
     *
     * @throws ValidationException|MissingCastTypeException|CastTargetException
     */
//    public static function fromModel(Model $model): self
//    {
//        return new static($model->toArray());
//    }

    /**
     * Creates a DTO instance from the given command arguments.
     *
     * @return $this
     *
     * @throws ValidationException|MissingCastTypeException|CastTargetException
     */
//    public static function fromCommandArguments(Command $command): self
//    {
//        return new static($command->arguments());
//    }

    /**
     * Creates a DTO instance from the given command options.
     *
     * @return $this
     *
     * @throws ValidationException|MissingCastTypeException|CastTargetException
     */
//    public static function fromCommandOptions(Command $command): self
//    {
//        return new static($command->options());
//    }

    /**
     * Creates a DTO instance from the given command arguments and options.
     *
     * @return $this
     *
     * @throws ValidationException|MissingCastTypeException|CastTargetException
     */
//    public static function fromCommand(Command $command): self
//    {
//        return new static(array_merge($command->arguments(), $command->options()));
//    }

    /**
     * Returns the DTO validated data in array format.
     */
    public function toArray(): array
    {
        return $this->buildDataForExport();
    }

    /**
     * Returns the DTO validated data in a JSON string format.
     */
    public function toJson(bool $pretty = false): string
    {
        return $pretty
            ? json_encode($this->buildDataForExport(), JSON_PRETTY_PRINT)
            : json_encode($this->buildDataForExport());
    }

    /**
     * Returns the DTO validated data in a pretty JSON string format.
     */
    public function toPrettyJson(): string
    {
        return json_encode($this->buildDataForExport(), JSON_PRETTY_PRINT);
    }

    /**
     * Creates a new model with the DTO validated data.
     */
//    public function toModel(string $model): Model
//    {
//        return new $model($this->buildDataForExport());
//    }

    /**
     * Cast the given value to a DTO instance.
     *
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @param  string  $key
     * @param  mixed  $value
     * @param  array  $attributes
     * @return $this
     *
     * @throws ValidationException|MissingCastTypeException|CastTargetException
     */
    public function get($model, $key, $value, $attributes)
    {
        $arrayCast = new ArrayCast();

        return new static($arrayCast->cast($key, $value));
    }

    /**
     * Prepare the value for storage.
     *
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @param  string  $key
     * @param  mixed  $value
     * @param  array  $attributes
     * @return string
     */
    public function set($model, $key, $value, $attributes)
    {
        if (is_string($value)) {
            return $value;
        }
        if (is_array($value)) {
            return json_encode($value);
        }
        if ($value instanceof ValidatedDTO) {
            return $value->toJson();
        }

        return '';
    }

    /**
     * Maps the DTO properties before the DTO instantiation.
     */
    protected function mapBeforeValidation(): array
    {
        return [];
    }

    /**
     * Maps the DTO properties before the DTO export.
     */
    protected function mapBeforeExport(): array
    {
        return [];
    }

    /**
     * Handles a passed validation attempt.
     *
     *
     * @throws MissingCastTypeException|CastTargetException
     */
    protected function passedValidation(): void
    {
        $this->validatedData = $this->validatedData();
        /** @var array<Castable> $casts */
        $casts = $this->casts();

        foreach ($this->validatedData as $key => $value) {
            $this->{$key} = $value;
        }

        foreach ($this->defaults() as $key => $value) {
            if (
                ! property_exists($this, $key) ||
                empty($this->{$key})
            ) {
                if (! array_key_exists($key, $casts)) {
                    if ($this->requireCasting) {
                        throw new MissingCastTypeException($key);
                    }

                    $this->{$key} = $value;
                    $this->validatedData[$key] = $value;

                    continue;
                }

                $formatted = $this->shouldReturnNull($key, $value)
                    ? null
                    : $this->castValue($casts[$key], $key, $value);
                $this->{$key} = $formatted;
                $this->validatedData[$key] = $formatted;
            }
        }
    }

    /**
     * Handles a failed validation attempt.
     */
    protected function failedValidation(): void
    {
        // Do nothing
    }

    /**
     * Checks if the data is valid for the DTO.
     */
    protected function isValidData(): bool
    {
        return true;
    }

    /**
     * Builds the validated data from the given data and the rules.
     *
     *
     * @throws MissingCastTypeException|CastTargetException
     */
    protected function validatedData(): array
    {
        $acceptedKeys = $this->getAcceptedProperties();
        $result = [];

        /** @var array<Castable> $casts */
        $casts = $this->casts();

        foreach ($this->data as $key => $value) {
            if (in_array($key, $acceptedKeys)) {
                if (! array_key_exists($key, $casts)) {
                    if ($this->requireCasting) {
                        throw new MissingCastTypeException($key);
                    }
                    $result[$key] = $value;

                    continue;
                }

                $result[$key] = $this->shouldReturnNull($key, $value)
                    ? null
                    : $this->castValue($casts[$key], $key, $value);
            }
        }

        foreach ($acceptedKeys as $property) {
            if (! array_key_exists($property, $result)) {
                $this->{$property} = null;
            }
        }

        return $result;
    }

    /**
     * @throws \Chunpat\ValidatedDTO\Exceptions\CastTargetException
     */
    protected function castValue($cast, string $key, $value)
    {
        if ($cast instanceof Castable) {
            return $cast->cast($key, $value);
        }

        if (! is_callable($cast)) {
            throw new CastTargetException($key);
        }

        return $cast($key, $value);
    }

    protected function shouldReturnNull(string $key, $value): bool
    {
        return is_null($value);
    }

    private function buildDataForValidation(array $data): array
    {
        return $this->mapData($this->mapBeforeValidation(), $data);
    }

    private function buildDataForExport(): array
    {
        return $this->mapData($this->mapBeforeExport(), $this->validatedData);
    }

    private function mapData(array $mapping, array $data): array
    {
        if (empty($mapping)) {
            return $data;
        }

        $mappedData = [];
        foreach ($data as $key => $value) {
            $properties = $this->getMappedProperties($mapping, $key);
            if ($properties !== [] && $this->isArrayable($value)) {
                $formatted = $this->formatArrayableValue($value);

                foreach ($properties as $property => $mappedValue) {
                    $mappedData[$mappedValue] = $formatted[$property] ?? null;
                }

                continue;
            }

            $property = array_key_exists($key, $mapping)
                ? $mapping[$key]
                : $key;

            $mappedData[$property] = $value;
        }

        return $mappedData;
    }

    private function getMappedProperties(array $mapping, string $key): array
    {
        $properties = [];
        foreach ($mapping as $mappedKey => $mappedValue) {
            if (str_starts_with($mappedKey, "{$key}.")) {
                $arrayKey = str_replace("{$key}.", '', $mappedKey);
                $properties[$arrayKey] = $mappedValue;
            }

            if (str_starts_with($mappedValue, "{$key}.")) {
                $arrayKey = str_replace("{$key}.", '', $mappedValue);
                $properties[$arrayKey] = $mappedKey;
            }
        }

        return $properties;
    }

    private function isArrayable($value): bool
    {
        return is_array($value) ||
            $value instanceof Collection ||
            $value instanceof ValidatedDTO ||
            $value instanceof Model ||
            is_object($value);
    }

    private function formatArrayableValue($value): array
    {
        if (is_array($value)) {
            return $value;
        }

        if (is_object($value)) {
            return (array) $value;
        }

        return $value->toArray();
    }

    /**
     * Inits the configuration for the DTOs.
     */
    private function initConfig(): void
    {
        $config = config('dto');
        if (! empty($config) && array_key_exists('require_casting', $config)) {
            $this->requireCasting = $config['require_casting'];
        }
    }

    private function getAcceptedProperties(): array
    {
        $acceptedKeys = [];
        foreach (get_class_vars(self::class) as $key => $value) {
            if (! $this->isforbiddenProperty($key)) {
                $acceptedKeys[] = $key;
            }
        }

        return $acceptedKeys;
    }

    private function isforbiddenProperty(string $property): bool
    {
        return in_array($property, [
            'data',
            'validatedData',
            'requireCasting',
            'validator',
        ]);
    }
}

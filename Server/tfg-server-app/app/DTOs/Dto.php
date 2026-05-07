<?php

namespace App\DTOs;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

abstract class Dto
{
    /**
     * Convert the DTO to an array.
     *
     * @return array
     */
    abstract public function toArray(): array;

    /**
     * Returns an array of the DTO without null values.
     *
     * @return array
     */
    public function toArrayWithoutNulls(): array
    {
        return array_filter($this->toArray(), fn($value) => $value !== null);
    }

    /**
     * Map a collection of models/data to a collection of DTOs.
     *
     * @param Collection|array $items
     * @return Collection
     */
    public static function collection(Collection|array $items): Collection
    {
        return collect($items)->map(fn($item) => static::fromModel($item));
    }

    /**
     * Map a model item to a DTO.
     *
     * @param mixed $item
     * @return static
     */
    abstract public static function fromModel($item): self;

    /**
     * Map an array to a DTO.
     *
     * @param array $data
     * @return static
     */
    abstract public static function fromArray(array $data): self;
}

<?php

declare(strict_types=1);

namespace DTO;

/**
 * A class that transforms the input data to the desired format
 */
class ProductDTO
{
    public string $make;
    public string $model;
    public string $colour;
    public string $capacity;
    public string $network;
    public string $grade;
    public string $condition;

    /**
     * Transforms the data into the desired format
     *
     * @param  array  $data
     * @return ProductDTO
     */
    public static function transform(array $data): ProductDTO
    {
        $dto = new self();
        $dto->make = $data['brand_name'];
        $dto->model = $data['model_name'];
        $dto->colour = $data['colour_name'] ?? null;
        $dto->capacity = $data['gb_spec_name'] ?? null;
        $dto->network = $data['network_name'] ?? null;
        $dto->grade = $data['grade_name'] ?? null;
        $dto->condition = $data['condition_name'] ?? null;

        return $dto;
    }

    /**
     * Returns the transformed data as an array
     *
     * @return array
     */
    public function toArray(): array
    {
        return get_object_vars($this);
    }

    /**
     * Returns the transformed data as an object
     *
     * @return \stdClass
     */
    public function toObject(): \stdClass
    {
        $object = new \stdClass();

        foreach (get_object_vars($this) as $key => $value) {
            $object->$key = $value;
        }

        return $object;
    }
}
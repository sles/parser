<?php

declare(strict_types=1);

namespace Classes;

use stdClass;

class Product
{
    public string $make;
    public string $model;
    public ?string $colour;
    public ?string $capacity;
    public ?string $network;
    public ?string $grade;
    public ?string $condition;

    /**
     * @param  array  $attributes
     */
    public function __construct(array $attributes = [])
    {
        $this->fillAttributes($attributes);
    }

    /**
     * Returns an array of attributes
     *
     * @return array
     */
    public function getAttributes(): array
    {
        return get_object_vars($this);
    }

    /**
     * Transform a class into an object
     *
     * @return stdClass
     */
    public function toObject(): stdClass
    {
        $object = new stdClass();

        foreach ($this->getAttributes() as $attribute => $value) {
            $object->$attribute = $value;
        }

        return $object;
    }


    /**
     * @param  array  $attributes
     * @return $this
     */
    public function fillAttributes(array $attributes = []): self
    {
        foreach ($attributes as $attribute => $value) {
            $this->$attribute = $value;
        }

        return $this;
    }
}
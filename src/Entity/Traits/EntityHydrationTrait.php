<?php

namespace App\Entity\Traits;

trait EntityHydrationTrait
{
    /**
     * Populates entity from given array
     * @param  array  $data
     * @return self
     */
    public static function fromArray(array $data) : self
    {
        $class = new static();
        foreach ($data as $property => $value) {
            if (true === is_array($value)) {
                $value = array_pop($value);
            }
            $method = "set{$property}";
            if (method_exists($class, $method)) {
                $class->$method($value);
            }
        }

        return $class;
    }
}

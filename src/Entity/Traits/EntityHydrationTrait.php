<?php

namespace App\Entity\Traits;

trait EntityHydrationTrait
{
    /**
     * Populates entity from given array.
     *
     * @param array $data
     *
     * @return mixed
     */
    public static function fromArray(array $data)
    {
        $class = new static();
        foreach ($data as $property => $value) {
            $method = "set{$property}";
            if (method_exists($class, $method)) {
                $classReflection = new \ReflectionClass($class);
                $methodReflection = $classReflection->getMethod($method);
                $methodParams = $methodReflection->getParameters();
                $typeHint = $methodParams[0]->getClass();

                if (null !== $typeHint && true === is_array($value)) {
                    $paramClass = $typeHint->newInstance();
                    $paramClass = new $paramClass();
                    $value = $paramClass::fromArray($value);
                } elseif (true === is_array($value)) {
                    $value = array_pop($value);
                }

                $class->$method($value);
            }
        }

        return $class;
    }
}

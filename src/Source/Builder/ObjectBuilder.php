<?php

namespace Source\Builder;

use Source\Type\ObjectType;
use Source\Type;

class ObjectBuilder implements \Source\BuilderInterface
{
    public function make($rawType): Type
    {
        if (is_string($rawType)) {
            $rawType = ['type' => 'object'];
        }

        $rawType += ['type' => 'object', 'description' => null, 'required' => null];

        $objectType = new ObjectType();
        $objectType->type = $rawType['type'];
        $objectType->description = $rawType['description'];
        $objectType->required = boolval($rawType['required']);

        $typeBuilder = new TypeBuilder;
        foreach ($rawType['properties'] as $propertyName => $rawProperty) {
            $poperty = $typeBuilder->make($rawProperty);
            $poperty->name = $propertyName;
            $objectType->properties[] = $poperty;
        }
        
        return $objectType;
    }
}


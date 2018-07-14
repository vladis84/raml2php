<?php

namespace Reader\Builder;

use Reader\BuilderFactory;
use Reader\Type;

/**
 *
 */
class TypeBuilder implements \Reader\BuilderInterface
{
    public function make(string $rawName, array $rawType): Type
    {
        if (is_string($rawType)) {
            $rawType = ['type' => $rawType];
        }

        $rawType += ['type' => null, 'description' => null, 'required' => null];

        $type              = new Type;
        $type->name        = $rawName;
        $type->type        = $rawType['type'];
        $type->description = $rawType['description'];
        $type->required    = (bool)$rawType['required'];

        $properties = $rawType['properties'] ?? [];
        $this->makeProperties($type, $properties);

        return $type;
    }

    private function makeProperties(Type $type, array $properties)
    {
        foreach ($properties as $rawName => $rawType) {
            $rawType = (array)$rawType;

            $typeBuilder        = BuilderFactory::make($rawType);
            $property           = $typeBuilder->make($rawName, $rawType);
            $type->properties[] = $property;
        }
    }
}

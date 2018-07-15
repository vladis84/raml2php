<?php

namespace Reader;

use Reader\{
    BuilderFactory
};

class TypeBuilder
{
    public function make(string $rawName, array $rawType): Type
    {
        $rawType = $this->normalizeRawType($rawType);

        $type              = new Type;
        $type->name        = $rawName;
        $type->type        = $rawType['type'];
        $type->description = $rawType['description'];
        $type->required    = (bool)$rawType['required'];

        $this->makeProperties($type, $rawType['properties']);

        return $type;
    }

    private function normalizeRawType($rawType): array
    {
        if (is_string($rawType)) {
            $rawType = ['type' => $rawType];
        }

        $rawType += ['type' => null, 'description' => null, 'required' => null, 'properties' => []];

        return $rawType;
    }

    private function makeProperties(Type $type, array $properties)
    {
        foreach ($properties as $rawName => $rawType) {
            $rawType = $this->normalizeRawType($rawType);

            $property           = $this->make($rawName, $rawType);
            $type->properties[] = $property;
        }
    }
}

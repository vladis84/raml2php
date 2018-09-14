<?php

namespace Parser;

use PhpCodeMaker\PhpClass;
use PhpCodeMaker\PhpClass\Property;

class PhpClassBuilder
{
    private static $typeMap = [
        'number' => 'float',
        'integer' => 'int',
        'date-only' => 'string Y-m-d'
    ];

    public static function build(array $data): PhpClass
    {
        $phpClass = new PhpClass();
        $phpClass->setName(ucfirst($data['__name__']));
        $phpClass->setNamespace($data['__nameSpace__']);

        $rawProperties = $data['properties'];
        foreach ($rawProperties as $rawProperty) {
            $property = self::makeProperty($rawProperty);
            $phpClass->addProperty($property);
        }

        return $phpClass;
    }

    private static function makeProperty(array $rawProperty): Property
    {
        $property = new Property();
        $property->setName($rawProperty['__name__']);

        if (isset($rawProperty['description'])) {
            $property->addPhpDoc($rawProperty['description']);
        }

        $propertyType = strtr($rawProperty['type'], self::$typeMap);
        if (isset($rawProperty['example'])) {
            $examples = (array) $rawProperty['example'];
            $propertyType .= ' ' . join(', ', $examples);
        }

        $property->addPhpDoc('@var', $propertyType);
        if (isset($rawProperty['required'])) {
            $property->addPhpDoc('@required');
        }

        if (isset($rawProperty['__link__'])) {
            $property->addPhpDoc('@see', $rawProperty['__link__']);
        }

        return $property;
    }
}

<?php

namespace Writer;

use PhpCodeMaker\PhpClass;
use PhpCodeMaker\PhpClass\Property;

class TypeObjectBuilder implements BuilderInterface
{
    public function build(array $data): PhpClass
    {
        $phpClass = new PhpClass();
        $phpClass->setName($data['__name__']);
        $phpClass->setNamespace($data['__nameSpace__']);

//        $uses = array_unique($data['__uses__']);
//        foreach ($uses as $use) {
//            $phpClass->addUse($use);
//        }

        $rawProperties = $data['properties'];
        foreach ($rawProperties as $rawProperty) {
            $property = $this->makeProperty($rawProperty);
            $phpClass->addProperty($property);
        }

        return $phpClass;
    }

    private function makeProperty(array $rawProperty): Property
    {
        $property = new Property();
        $property->setName($rawProperty['__name__']);

        if (isset($rawProperty['description'])) {
            $property->addPhpDoc($rawProperty['description']);
        }

        $propertyType = $rawProperty['type'];
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

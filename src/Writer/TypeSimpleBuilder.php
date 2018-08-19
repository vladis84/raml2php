<?php

namespace Writer;

use PhpCodeMaker\PhpClass;
use PhpCodeMaker\PhpClass\Method;
use PhpCodeMaker\PhpClass\Property;

class TypeSimpleBuilder implements BuilderInterface
{
    public function build(array $data): PhpClass
    {
        $phpClass = new PhpClass();

        $phpClass
            ->setName($data['__name__'])
            ->setNamespace($data['__nameSpace__'])
            ->setImplements(['\\JsonSerializable'])
        ;

        $phpMethod = new Method();
        $phpMethod
            ->setName('jsonSerialize')
            ->setVisiblityPublic()
            ->setCode('return $this->result;')
        ;
        $phpClass->addMethod($phpMethod);

        if (isset($data['description'])) {
            $phpClass->setDescription($data['description']);
        }

        $property = new Property();
        $property->setName('result');

        $propertyType = $data['type'];
        if (isset($data['example'])) {
            $examples = (array) $data['example'];
            $propertyType .= ' ' . join(', ', $examples);
        }

        $property->addPhpDoc('@var', $propertyType);
        if (isset($data['required'])) {
            $property->addPhpDoc('@required');
        }

        if (isset($data['__link__'])) {
            $property->addPhpDoc('@see', $data['__link__']);
        }

        $phpClass->addProperty($property);

        return $phpClass;
    }
}

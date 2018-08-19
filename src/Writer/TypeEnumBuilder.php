<?php

namespace Writer;

use PhpCodeMaker\PhpClass;
use PhpCodeMaker\PhpClass\Constant;

class TypeEnumBuilder implements BuilderInterface
{
    public function build(array $data): PhpClass
    {
        $phpClass = new PhpClass();
        $phpClass
            ->setName($data['__name__'])
            ->setNamespace($data['__nameSpace__'])
        ;

        $constants = $data['enum'];
        $this->addConstants($phpClass, $constants);

        return $phpClass;
    }

    private function addConstants(PhpClass $phpClass, array $rawConstants)
    {
        foreach ($rawConstants as $rawConstant) {
            $constant = new Constant();

            $constant
                ->setName($rawConstant)
                ->setValue($rawConstant)
            ;

            $phpClass->addConstant($constant);
        }
    }

}

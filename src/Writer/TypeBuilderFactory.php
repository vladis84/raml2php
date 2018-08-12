<?php

namespace Writer;

class TypeBuilderFactory
{
    public static function make(array $type): BuilderInterface
    {
        if ($type['type'] == 'object') {
            $builder = new TypeObjectBuilder();
        } elseif ($type['type'] == 'enum') {
            $builder = new TypeEnumBuilder();
        }

        return $builder;
    }
}

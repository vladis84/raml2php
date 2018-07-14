<?php

namespace Reader;

use Reader\Builder\{ObjectBuilder, TypeBuilder};

class BuilderFactory
{
    public static function make(array $rawType): BuilderInterface
    {
        $type = $rawType['type'] ?? $rawType;

        return new TypeBuilder;
    }
}


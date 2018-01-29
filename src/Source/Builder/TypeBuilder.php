<?php

namespace Source\Builder;

use Source\Type;

/**
 *
 */
class TypeBuilder implements \Source\BuilderInterface
{
    public function make($rawType): Type
    {
        if (is_string($rawType)) {
            $rawType = ['type' => $rawType];
        }

        $rawType += ['type' => null, 'description' => null, 'required' => null];
        $type = new Type;

        $type->type = $rawType['type'];
        $type->description = $rawType['description'];
        $type->required = boolval($rawType['required']);

        return $type;
    }
}

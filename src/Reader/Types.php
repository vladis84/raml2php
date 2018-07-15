<?php

namespace Reader;

/**
 */
class Types
{
    /**
     * @var Type[]
     */
    private $list = [];

    public function addType(Type $type)
    {
        $this->list[] = $type;
    }

    /**
     * @return Type[]
     */
    public function getList(): array
    {
        return $this->list;
    }
}


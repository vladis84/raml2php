<?php

namespace Reader;

class Source
{
    /**
     *
     * @var Types
     */
    private $types;

    /**
     * @var
     */
    private $resources;

    public function __construct()
    {
        $this->types = new Types();
    }

    public function addType(Type $type)
    {
        $this->types->addType($type);
    }

    /**
     * @return Types
     */
    public function getTypes(): Types
    {
        return $this->types;
    }

    /**
     * @return mixed
     */
    public function getResources()
    {
        return $this->resources;
    }
}


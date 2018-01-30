<?php

use Source\Types;

class Source
{
    /**
     *
     * @var Types
     */
    public $types;
    
    public function __construct()
    {
        $this->types = new Types();
    }
}


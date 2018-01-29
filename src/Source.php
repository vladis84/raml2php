<?php

use Source\Types;

class Source
{
    public $types;
    
    public function __construct()
    {
        $this->types = new Types();
    }
}


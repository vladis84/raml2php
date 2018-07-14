<?php

namespace Reader;

interface BuilderInterface
{
    public function make(string $rawName, array $rawType): Type;
}


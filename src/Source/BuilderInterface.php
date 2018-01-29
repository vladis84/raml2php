<?php

namespace Source;

interface BuilderInterface
{
    public function make($rawType): Type;
}


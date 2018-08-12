<?php

namespace Writer;

use PhpCodeMaker\PhpClass;

interface BuilderInterface
{
    public function build(array $data): PhpClass;
}

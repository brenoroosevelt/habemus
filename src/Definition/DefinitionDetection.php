<?php
declare(strict_types=1);

namespace Habemus\Definition;

interface DefinitionDetection
{
    public function detect($value): Definition;
}

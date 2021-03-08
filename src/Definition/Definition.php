<?php
declare(strict_types=1);

namespace Habemus\Definition;

use Psr\Container\ContainerInterface;

interface Definition
{
    public function getConcrete(ContainerInterface $container);
}

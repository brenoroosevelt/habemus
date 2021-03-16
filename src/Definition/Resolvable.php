<?php
declare(strict_types=1);

namespace Habemus\Definition;

use Psr\Container\ContainerInterface;

interface Resolvable
{
    public function getConcrete(ContainerInterface $container);
}

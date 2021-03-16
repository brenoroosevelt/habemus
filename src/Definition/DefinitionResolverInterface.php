<?php
declare(strict_types=1);

namespace Habemus\Definition;

use Habemus\Definition\Definition;

interface DefinitionResolverInterface
{
    /**
     * @param Definition $definition
     * @return mixed
     */
    public function resolve(Definition $definition);

    /**
     * @param Definition ...$definitions
     * @return array
     */
    public function resolveMany(Definition ...$definitions): array;
}

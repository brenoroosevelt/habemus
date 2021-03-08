<?php
declare(strict_types=1);

namespace Habemus\Definition;

use Habemus\Definition\Definition;

interface DefinitionResolverInterface
{
    /**
     * @param string $id
     * @param Definition $definition
     * @return mixed
     */
    public function resolve(string $id, Definition $definition);

    /**
     * @param Definition[] $definitions Indexed by definition ID
     * @return array
     */
    public function resolveMany(array $definitions = []): array;
}

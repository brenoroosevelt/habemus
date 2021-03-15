<?php
declare(strict_types=1);

namespace Habemus\Autowire;

interface ClassResolver
{
    /**
     * Try to get an instance of a given class
     * @param string $className Qualified class name
     * @param array $constructorArguments Pre-defined constructor arguments
     * @return mixed An instance
     */
    public function resolveClass(string $className, array $constructorArguments = []);
    public function canResolve(string $className): bool;
}

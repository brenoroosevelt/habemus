<?php
declare(strict_types=1);

namespace Habemus\Autowire;

interface ClassResolver
{
    /**
     * Try to get an instance of a given class
     * @param string $className Qualified class name
     * @param array $constructorArguments Pre-defined constructor arguments
     * @return mixed An instace
     */
    public function resolveClass(string $className, array $constructorArguments = []);
}

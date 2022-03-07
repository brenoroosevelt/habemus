<?php
declare(strict_types=1);

namespace Habemus\Definition;

use Closure;
use Habemus\Definition\Build\ArrayDefinition;
use Habemus\Definition\Build\ClassDefinition;
use Habemus\Definition\Build\FactoryDefinition;
use Habemus\Definition\Build\FnDefinition;
use Habemus\Definition\Build\ReferenceDefinition;
use Habemus\Definition\Build\RawDefinition;
use Habemus\Definition\Build\IterateDefinition;
use Habemus\Exception\InvalidDefinitionException;

/**
 * @method static ReferenceDefinition use(string $id, Closure $fn = null)
 * @method static ReferenceDefinition reference(string $id, Closure $fn = null)
 * @method static ReferenceDefinition entry(string $id, Closure $fn = null)
 * @method static RawDefinition raw(mixed $value)
 * @method static ClassDefinition class(string $class, array $args = [])
 * @method static FactoryDefinition factory($class, string $method, array $params = [], bool $static = false)
 * @method static ArrayDefinition array(array $arr, bool $recursive = false)
 * @method static FnDefinition fn(Closure $fn)
 * @method static IterateDefinition iterate(string ...$id)
 */
trait DefinitionBuilder
{
    public static function __callStatic($name, $arguments): Definition
    {
        $namespace = __NAMESPACE__ . "\\Build";
        $class = sprintf("%s\%sDefinition", $namespace, ucfirst($name));
        if (!class_exists($class)) {
            throw InvalidDefinitionException::unavailable($name);
        }

        return new $class(...$arguments);
    }
}

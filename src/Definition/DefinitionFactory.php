<?php
declare(strict_types=1);

namespace Habemus\Definition;

use Habemus\Definition\Available\ArrayDefinition;
use Habemus\Definition\Available\CallbackDefinition;
use Habemus\Definition\Available\ClassDefinition;
use Habemus\Definition\Available\FactoryDefinition;
use Habemus\Definition\Available\FnDefinition;
use Habemus\Definition\Available\IdDefinition;
use Habemus\Definition\Available\IdsDefinition;
use Habemus\Definition\Available\RawDefinition;
use Habemus\Definition\Available\IterateDefinition;

/**
 * @method static IdDefinition id(string $id)
 * @method static IdsDefinition ids(string ...$id)
 * @method static RawDefinition raw(mixed $value)
 * @method static ClassDefinition class(string $class, array $args = [])
 * @method static FactoryDefinition factory($class, string $method, array $params = [], bool $static = false)
 * @method static ArrayDefinition array(array $arr, bool $recursive = false)
 * @method static FnDefinition fn(callable $fn)
 * @method static IterateDefinition iterate(string ...$id)
 * @method static CallbackDefinition callback(string $id, callable $fn)
 */
trait DefinitionFactory
{
    public static function __callStatic($name, $arguments): Definition
    {
        $namespace = __NAMESPACE__ . "\\Available";
        $class = sprintf("%s\%sDefinition", $namespace, ucfirst($name));
        if (!class_exists($class)) {
            throw new \RuntimeException(sprintf("Definition type (%s) not found.", $name));
        }

        return new $class(...$arguments);
    }
}

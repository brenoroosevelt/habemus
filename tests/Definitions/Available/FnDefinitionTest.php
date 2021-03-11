<?php
declare(strict_types=1);

namespace Habemus\Test\Definitions\Available;

use Habemus\Container;
use Habemus\Definition\Available\FnDefinition;
use Habemus\Test\TestCase;
use Psr\Container\ContainerInterface;
use stdClass;

class FnDefinitionTest extends TestCase
{
    public function testShouldResolveFnDefinition()
    {
        $definition = new FnDefinition(function () {
            return new stdClass();
        });
        $value = $definition->getConcrete(new Container());
        $this->assertInstanceOf(stdClass::class, $value);
    }

    public function testShouldFnDefinitionParameterInstanceOfContainerPsr11()
    {
        $definition = new FnDefinition(function ($container) {
            $this->assertInstanceOf(ContainerInterface::class, $container);
        });
        $definition->getConcrete(new Container());
    }
}

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
    public function testShouldCreateAndResolveFnDefinition()
    {
        $definition = new FnDefinition(function ($container) {
            $this->assertInstanceOf(ContainerInterface::class, $container);
            return new stdClass();
        });
        $value = $definition->getConcrete(new Container());
        $this->assertInstanceOf(stdClass::class, $value);
    }
}

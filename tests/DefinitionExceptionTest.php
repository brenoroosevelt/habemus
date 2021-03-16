<?php
declare(strict_types=1);

namespace Habemus\Test;

use Habemus\Definition\Definition;
use Habemus\Exception\DefinitionException;
use Psr\Container\ContainerInterface;

class DefinitionExceptionTest extends TestCase
{
    public function testShouldDefinitionExceptionGetDefinition()
    {
        $definition = new class implements Definition {
            public function getConcrete(ContainerInterface $container)
            {
                return 1;
            }
        };
        $exception = DefinitionException::unshareable($definition);
        $this->assertSame($definition, $exception->getDefinition());
    }
}

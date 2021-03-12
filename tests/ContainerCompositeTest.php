<?php
declare(strict_types=1);

namespace Habemus\Test;

use Habemus\Container;
use Habemus\ContainerComposite;
use Habemus\Exception\NotFound;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;

class ContainerCompositeTest extends TestCase
{
    protected function newContainerPsr11(array $values)
    {
        return new class($values) implements ContainerInterface {
            protected $values = [];

            public function __construct(array $values)
            {
                $this->values = $values;
            }

            public function get($id)
            {
                if (!array_key_exists($id, $this->values)) {
                    throw NotFound::noEntryWasFound($id);
                }
                return $this->values[$id];
            }

            public function has($id)
            {
                return array_key_exists($id, $this->values);
            }
        };
    }

    public function testShouldAddContainersToTheComposition()
    {
        $container1 = $this->newContainerPsr11([
            'a1' => 'value a1',
            'a2' => 'value a2',
        ]);

        $container2 = $this->newContainerPsr11([
            'b1' => 'value b1',
        ]);

        $composite = new ContainerComposite();
        $composite->add($container1);
        $composite->add($container2);
        $this->assertTrue($composite->has('a1'));
        $this->assertTrue($composite->has('a2'));
        $this->assertTrue($composite->has('b1'));
    }

    public function testShouldReturnValueFromPrioritizedContainers()
    {
        $container1 = $this->newContainerPsr11([
            'a1' => 1,
        ]);

        $container2 = $this->newContainerPsr11([
            'a1' => 2,
        ]);

        $composite = new ContainerComposite();
        $composite->add($container1, 2);
        $composite->add($container2, 1);
        $this->assertEquals(2, $composite->get('a1'));
    }

    public function testShouldReplaceContainerCompositeWithAnotherPriority()
    {
        $container1 = $this->newContainerPsr11([
            'a1' => 1,
        ]);

        $container2 = $this->newContainerPsr11([
            'a1' => 2,
        ]);

        $composite = new ContainerComposite();
        $composite->add($container1, 1);
        $composite->add($container2, 2);
        $composite->add($container1, 3);
        $this->assertEquals(2, $composite->get('a1'));
    }

    public function testShouldGetPsrExceptionIfNotFoundInContainerComposite()
    {
        $composite = new ContainerComposite();
        $this->expectException(ContainerExceptionInterface::class);
        $composite->get('a1');
    }
}

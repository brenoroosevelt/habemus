<?php
declare(strict_types=1);

namespace Habemus\Test;

use Habemus\ContainerComposite;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use stdClass;

class ContainerCompositeTest extends TestCase
{
    public function testShouldCreateEmptyContainerComposite()
    {
        $composite = new ContainerComposite();
        $this->assertInstanceOf(ContainerInterface::class, $composite);
    }

    public function testShouldCreateContainerCompositeAddingContainers()
    {
        $container1 = $this->newContainerPsr11([
            'a1' => 'value a1',
            'a2' => 'value a2',
        ]);

        $container2 = $this->newContainerPsr11([
            'b1' => 'value b1',
        ]);

        $composite = new ContainerComposite([$container1, $container2]);
        $this->assertTrue($composite->has('a1'));
        $this->assertTrue($composite->has('a2'));
        $this->assertTrue($composite->has('b1'));
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

    public function testShouldCreateWithConstructorAndReturnPrioritizedValue()
    {
        $container1 = $this->newContainerPsr11([
            'a1' => 1,
        ]);

        $container2 = $this->newContainerPsr11([
            'a1' => 2,
        ]);

        $composite = new ContainerComposite([$container2, $container1]); // Priority: $container2
        $this->assertEquals(2, $composite->get('a1'));
    }

    public function testShouldCreateWithConstructorSkippingNonPsr11()
    {
        $container1 = $this->newContainerPsr11([
            'a1' => 1,
        ]);

        $container2 = $this->newContainerPsr11([
            'b1' => 2,
        ]);

        $composite = new ContainerComposite([$container2, $container1, new stdClass(), 123]); // Priority: $container2
        $this->assertTrue($composite->has('a1'));
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
        $this->expectException(NotFoundExceptionInterface::class);
        $composite->get('a1');
    }

    public function testShouldAddInDefaultPriorityOrder()
    {
        $container1 = $this->newContainerPsr11([
            'a1' => 1,
        ]);

        $container2 = $this->newContainerPsr11([
            'a1' => 2,
        ]);

        $container3 = $this->newContainerPsr11([
            'a1' => 3,
        ]);

        $composite = new ContainerComposite();
        $composite->add($container1, 3);
        $composite->add($container2, 1);
        $composite->add($container3);
        $this->assertEquals(2, $composite->get('a1'));
        $this->assertEquals(1, $composite->getHighestPriority());
        $this->assertEquals(4, $composite->getLowestPriority());
    }
}

<?php
declare(strict_types=1);

namespace Habemus\Test;

use Composer\Factory;
use Habemus\Container;
use Habemus\Test\Fixtures\ClassC;
use Habemus\Test\Fixtures\GenericInterface;
use Psr\Container\ContainerInterface;

class Sample
{
    public function sample()
    {
        $container = new Container();
        $container->add(GenericInterface::class, ClassC::class);
        $container->get(ClassC::class);

        $container->add(
            'someId',
            Container::factory(Factory::class, 'createMethod')
        );

        $container->add(
            'someId',
            function ($container) {
                return new \stdClass();
            }
        )->shared(true);
    }
}

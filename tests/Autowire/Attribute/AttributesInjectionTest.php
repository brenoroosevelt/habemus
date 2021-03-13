<?php
declare(strict_types=1);

namespace Habemus\Test\Autowire\Attribute;

use Habemus\Autowire\Attributes\AttributesInjection;
use Habemus\Autowire\Reflector;
use Habemus\Container;
use Habemus\Test\TestCase;

class AttributesInjectionTest extends TestCase
{
    /**
     * @var Container
     */
    protected $container;

    /**
     * @var Reflector
     */
    protected $reflector;

    /**
     * @var AttributesInjection
     */
    protected $attributesInjection;

    protected function setUp(): void
    {
        $this->container = new Container();
        $this->reflector = new Reflector();
        $this->attributesInjection = new AttributesInjection($this->container, $this->reflector);
        parent::setUp();
    }

    protected function tearDown(): void
    {
        unset($this->attributesInjection);
        unset($this->reflector);
        unset($this->container);
        parent::tearDown();
    }

    public function testShouldGetInjectionFromAttributes()
    {
        new class {
            protected int $c;
        };
        
        $this->assertTrue(true);
    }
}

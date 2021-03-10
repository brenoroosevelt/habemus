<?php
declare(strict_types=1);

namespace Habemus\Test\Definitions;

use Habemus\Container;
use Habemus\Definition\AutoDetection;
use Habemus\Definition\Available\ClassDefinition;
use Habemus\Definition\Available\FnDefinition;
use Habemus\Definition\Available\RawDefinition;
use Habemus\Test\Fixtures\ClassA;
use Habemus\Test\TestCase;

class AutoDetectionTest extends TestCase
{
    public function providerDefinitions()
    {
        return [
            'raw_definition'=> [
                RawDefinition::class,
                new RawDefinition(1)
            ],
            'number_int'=> [
                RawDefinition::class,
                999
            ],
            'number_float'=> [
                RawDefinition::class,
                50.5
            ],
            'strings'=> [
                RawDefinition::class,
                "strings"
            ],
            'array'=> [
                RawDefinition::class,
                ["strings", 1, 5.0]
            ],
            'objects'=> [
                RawDefinition::class,
                new \stdClass()
            ],
            'objects_2'=> [
                RawDefinition::class,
                new ClassA()
            ],
            'closure_inline'=> [
                FnDefinition::class,
                function () {
                }
            ],
            'raw_closure_object'=> [
                RawDefinition::class,
                new class {
                    public function __invoke()
                    {
                    }
                }
            ],
            'class_autowire'=> [
                ClassDefinition::class,
                ClassA::class
            ],
        ];
    }

    /**
     * @dataProvider providerDefinitions
     */
    public function testShouldDetectDefinitionsInstances($expected, $value)
    {
        $detection = new AutoDetection(new Container());
        $result = $detection->detect($value);
        $this->assertInstanceOf($expected, $result);
    }
}

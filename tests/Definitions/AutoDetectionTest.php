<?php
declare(strict_types=1);

namespace Habemus\Test\Definitions;

use Habemus\Container;
use Habemus\Definition\AutoDetection;
use Habemus\Definition\Build\ArrayDefinition;
use Habemus\Definition\Build\ClassDefinition;
use Habemus\Definition\Build\FnDefinition;
use Habemus\Definition\Build\ReferenceDefinition;
use Habemus\Definition\Build\RawDefinition;
use Habemus\Definition\Definition;
use Habemus\Definition\Identifiable\IdentifiableTrait;
use Habemus\Test\Fixtures\ClassA;
use Habemus\Test\TestCase;
use Habemus\Utility\PHPVersion;
use Psr\Container\ContainerInterface;

class AutoDetectionTest extends TestCase
{
    public function providerDefinitions()
    {
        $valuesToDetect =  [
            'raw_definition'=> [
                RawDefinition::class,
                new RawDefinition(1)
            ],
            'a_definition'=> [
                Definition::class,
                new class implements Definition {
                    use IdentifiableTrait;
                    public function getConcrete(ContainerInterface $container)
                    {
                        return 1;
                    }
                }
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
            'objects_ClassA'=> [
                RawDefinition::class,
                new ClassA()
            ],
            'closure_inline'=> [
                FnDefinition::class,
                function () {
                }
            ],
            'object_closure'=> [
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
            'array_with_id_alias'=> [
                ArrayDefinition::class,
                [1, 2, [new ReferenceDefinition('id1')]]
            ],
            'null_value'=> [
                RawDefinition::class,
                null
            ],
            'empty_array'=> [
                RawDefinition::class,
                []
            ],
            'zero'=> [
                RawDefinition::class,
                0
            ],
            'boolean_true'=> [
                RawDefinition::class,
                true
            ],
            'boolean_false'=> [
                RawDefinition::class,
                false
            ],
            'object_toString' => [
                RawDefinition::class,
                new class {
                    public function __toString(): string
                    {
                        return "str";
                    }
                }
            ]
        ];

        if (PHPVersion::current() >= PHPVersion::V8_0) {
            $valuesToDetect['implements_Stringable'] = [
                RawDefinition::class,
                new class implements \Stringable {
                    public function __toString(): string
                    {
                        return "str";
                    }
                }
            ];
        }

        return $valuesToDetect;
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

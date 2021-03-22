<?php
declare(strict_types=1);

namespace Habemus\Autowiring\Parameter;

use Habemus\Autowiring\Attributes\AttributesInjection;
use Habemus\Autowiring\Reflector;
use Habemus\Container;
use ReflectionParameter;

class ParameterResolverChain implements ParameterResolver
{
    /**
     * @var ParameterResolver[]
     */
    protected $chain;

    public function __construct(ParameterResolver ...$chain)
    {
        $this->chain = $chain;
    }

    /**
     * @inheritDoc
     */
    public function resolve(ReflectionParameter $parameter, array $arguments, array &$result): bool
    {
        foreach ($this->chain as $resolver) {
            if ($resolver->resolve($parameter, $arguments, $result)) {
                return true;
            }
        }

        return false;
    }

    public static function default(Container $container, AttributesInjection $injection, Reflector $reflector): self
    {
        return new self(
            new UserDefinedParameterResolver(),
            new InjectionParameterResolver($container, $injection),
            new DefaultValueParameterResolver(),
            new NullableParameterResolver(),
            new OptionalParameterResolver(),
            new TypeHintParameterResolver($container, $reflector)
        );
    }
}

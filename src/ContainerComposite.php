<?php
declare(strict_types=1);

namespace Habemus;

use Habemus\Exception\NotFound;
use Habemus\Util\Lists\ObjectPriorityList;
use Psr\Container\ContainerInterface;

class ContainerComposite implements ContainerInterface
{
    const DEFAULT_PRIORITY = 999;

    /**
     * @var ObjectPriorityList
     */
    protected $containers;

    /**
     * Skips array elements that do not implement ContainerInterface (PSR-11)
     * @param array $containers Containers indexed by priority
     */
    public function __construct(array $containers = [])
    {
        $this->containers = new ObjectPriorityList();
        $filtered = array_filter($containers, function ($item) {
            return $item instanceof ContainerInterface;
        });
        foreach ($filtered as $priority => $container) {
            $this->add($container, (int) $priority);
        }
    }

    /**
     * @param ContainerInterface $container
     * @param int $priority
     */
    public function add(ContainerInterface $container, int $priority = self::DEFAULT_PRIORITY): void
    {
        $this->containers->add($container, $priority);
    }

    /**
     * @inheritDoc
     */
    public function has($id): bool
    {
        foreach ($this->containers as $container) {
            if ($container->has($id)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @inheritDoc
     */
    public function get($id)
    {
        foreach ($this->containers as $container) {
            if ($container->has($id)) {
                return $container->get($id);
            }
        }

        throw NotFound::noEntryWasFound($id);
    }
}

<?php
declare(strict_types=1);

namespace Habemus;

use Habemus\Exception\NotFound;
use Habemus\Util\Lists\ObjectPriorityList;
use Psr\Container\ContainerInterface;

class ContainerComposite implements ContainerInterface
{
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
     * If priority is empty, appends with the lowest priority
     * @param ContainerInterface $container
     * @param int|null $priority
     * @return $this
     */
    public function add(ContainerInterface $container, ?int $priority = null): self
    {
        if ($priority === null) {
            $priority = $this->getLowestPriority() + 1;
        }

        $this->containers->add($container, $priority);
        return $this;
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

    public function getLowestPriority(): int
    {
        return (int) $this->containers->getLowestPriority();
    }

    public function getHighestPriority(): int
    {
        return (int) $this->containers->getHighestPriority();
    }
}

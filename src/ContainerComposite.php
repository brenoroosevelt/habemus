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

    public function __construct()
    {
        $this->containers = new ObjectPriorityList();
    }

    public function add(ContainerInterface $container, int $priority = self::DEFAULT_PRIORITY): void
    {
        $this->containers->add($container, $priority);
    }

    public function has($id): bool
    {
        foreach ($this->containers as $container) {
            if ($container->has($id)) {
                return true;
            }
        }

        return false;
    }

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

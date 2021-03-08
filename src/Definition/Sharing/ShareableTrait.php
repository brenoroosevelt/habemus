<?php
declare(strict_types=1);

namespace Habemus\Definition\Sharing;

trait ShareableTrait
{
    /**
     * @var bool
     */
    protected $shared = true;

    public function isShared(): bool
    {
        return $this->shared;
    }

    public function setShared(bool $share): self
    {
        $this->shared = $share;
        return $this;
    }
}

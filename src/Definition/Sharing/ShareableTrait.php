<?php
declare(strict_types=1);

namespace Habemus\Definition\Sharing;

trait ShareableTrait
{
    /**
     * @var bool|null
     */
    protected $shared = null;

    public function isShared(): ?bool
    {
        return $this->shared;
    }

    public function setShared(bool $share)
    {
        $this->shared = $share;
        return $this;
    }
}

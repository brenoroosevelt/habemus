<?php
declare(strict_types=1);

namespace Habemus\Definition\Sharing;

interface Shareable
{
    public function isShared(): ?bool;

    /**
     * @param bool $share
     * @return $this
     */
    public function setShared(bool $share);
}

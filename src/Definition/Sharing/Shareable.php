<?php
declare(strict_types=1);

namespace Habemus\Definition\Sharing;

interface Shareable
{
    public function isShared(): bool;
    public function setShared(bool $share): self;
}

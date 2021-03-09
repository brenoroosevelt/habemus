<?php
declare(strict_types=1);

namespace Habemus\Definition\Tag;

interface Taggable
{
    /**
     * @param string $tag
     * @return self
     */
    public function addTag(string $tag);
    public function hasTag(string $tag): bool;
    public function getTags(): array;
}

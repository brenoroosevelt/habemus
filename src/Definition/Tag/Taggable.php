<?php
declare(strict_types=1);

namespace Habemus\Definition\Tag;

interface Taggable
{
    public function addTag(string $tag): self;
    public function hasTag(string $tag): bool;
    public function getTags(): array;
}

<?php
declare(strict_types=1);

namespace Habemus\Definition\Tag;

trait TaggableTrait
{

    /**
     * @var array
     */
    protected $tags = [];

    public function addTag(string $tag): self
    {
        $this->tags[] = $tag;
        return $this;
    }

    public function hasTag(string $tag): bool
    {
        return in_array($tag, $this->tags);
    }

    public function getTags(): array
    {
        return $this->tags;
    }
}

<?php
declare(strict_types=1);

namespace Habemus\Definition\Identifiable;

trait IdentifiableTrait
{
    /**
     * @var string|null
     */
    protected $identity;

    public function setIdentity(string $id)
    {
        $this->identity = $id;
        return $this;
    }

    public function getIdentity(): ?string
    {
        return $this->identity;
    }
}

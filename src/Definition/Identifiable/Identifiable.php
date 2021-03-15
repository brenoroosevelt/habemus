<?php
declare(strict_types=1);

namespace Habemus\Definition\Identifiable;

interface Identifiable
{
    /**
     * @param string $id
     * @return self
     */
    public function setIdentity(string $id);

    /**
     * @return string|null
     */
    public function getIdentity(): ?string;
}

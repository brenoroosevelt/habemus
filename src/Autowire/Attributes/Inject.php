<?php
declare(strict_types=1);

namespace Habemus\Autowire\Attributes;

#[\Attribute]
class Inject
{
    /**
     * @var string|null
     */
    protected $id;

    public function __construct(?string $id = null)
    {
        $this->id = $id;
    }

    public function getId(): ?string
    {
        return $this->id;
    }
}

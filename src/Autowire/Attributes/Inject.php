<?php
declare(strict_types=1);

namespace Habemus\Autowire\Attributes;

#[\Attribute]
class Inject
{
    /**
     * @var string|null
     */
    public $id;

    public function __construct(?string $id = null)
    {
        $this->id = $id;
    }
}

<?php

namespace BiiiiiigMonster\Cleans\Attributes;

use Attribute;
use BiiiiiigMonster\Cleans\Contracts\CleansAttributes;

#[Attribute(Attribute::TARGET_METHOD)]
class Clean
{
    /**
     * Clean constructor.
     *
     * @param CleansAttributes|null $condition
     * @param bool $cleanWithSoftDelete
     * @param string|null $cleanQueue
     */
    public function __construct(
        public ?CleansAttributes $condition = null,
        public bool $cleanWithSoftDelete = true,
        public ?string $cleanQueue = null
    )
    {
    }
}
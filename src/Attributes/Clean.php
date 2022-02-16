<?php

namespace BiiiiiigMonster\Cleanable\Attributes;

use Attribute;
use BiiiiiigMonster\Cleanable\Contracts\CleanableAttributes;

#[Attribute(Attribute::TARGET_METHOD)]
class Clean
{
    /**
     * Clean constructor.
     *
     * @param CleanableAttributes|null $condition
     * @param bool $cleanWithSoftDelete
     * @param string|null $cleanQueue
     */
    public function __construct(
        public ?CleanableAttributes $condition = null,
        public bool $cleanWithSoftDelete = true,
        public ?string $cleanQueue = null
    )
    {
    }
}
<?php

namespace Biiiiiigmonster\Cleanable\Attributes;

use Attribute;
use Biiiiiigmonster\Cleanable\Contracts\CleanableAttributes;

#[Attribute(Attribute::TARGET_METHOD)]
class Clean
{
    /**
     * Clean constructor.
     *
     * @param CleanableAttributes|null $condition
     * @param bool $propagateSoftDelete
     * @param string|null $cleanQueue
     */
    public function __construct(
        public ?CleanableAttributes $condition = null,
        public bool $propagateSoftDelete = true,
        public ?string $cleanQueue = null
    )
    {
    }
}
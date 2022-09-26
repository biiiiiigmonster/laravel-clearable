<?php

namespace BiiiiiigMonster\Clears\Attributes;

use Attribute;

#[Attribute(Attribute::TARGET_METHOD)]
class Clear
{
    /**
     * Clear constructor.
     *
     * @param string|null $clearsAttributesClassName
     * @param string|null $clearQueue
     */
    public function __construct(
        public ?string $clearsAttributesClassName = null,
        public ?string $clearQueue = null
    ) {
    }
}

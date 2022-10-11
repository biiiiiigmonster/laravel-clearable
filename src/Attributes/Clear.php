<?php

namespace BiiiiiigMonster\Clearable\Attributes;

use Attribute;

#[Attribute(Attribute::TARGET_METHOD)]
class Clear
{
    /**
     * Clear constructor.
     *
     * @param string|null $clearsAttributesClassName
     * @param string|bool|null $clearQueue
     */
    public function __construct(
        public ?string $clearsAttributesClassName = null,
        public string|bool|null $clearQueue = null,
    ) {
    }
}

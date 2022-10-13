<?php

namespace BiiiiiigMonster\Clearable\Attributes;

use Attribute;

#[Attribute(Attribute::TARGET_METHOD)]
class Clear
{
    /**
     * Clear constructor.
     *
     * @param string|null $invokableClearClassName
     * @param string|null $clearQueue
     * @param string|null $clearConnection
     */
    public function __construct(
        public ?string $invokableClearClassName = null,
        public string|null $clearQueue = null,
        public string|null $clearConnection = null,
    ) {
    }
}

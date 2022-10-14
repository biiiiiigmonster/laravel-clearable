<?php

namespace BiiiiiigMonster\Clearable\Attributes;

use Attribute;
use BiiiiiigMonster\Clearable\ClearManager;

#[Attribute(Attribute::TARGET_METHOD)]
class Clear
{
    /**
     * Clear constructor.
     *
     * @param string|null $invokableClearClassName
     * @param string|null $clearQueue
     * @param ?string $clearConnection
     */
    public function __construct(
        public ?string $invokableClearClassName = null,
        public ?string $clearQueue = null,
        public ?string $clearConnection = ClearManager::SYNC_QUEUE_CONNECTION,
    ) {
    }
}

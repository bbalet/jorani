<?php

/*
 * This file is part of the Phony package.
 *
 * Copyright © 2016 Erin Millard
 *
 * For the full copyright and license information, please view the LICENSE file
 * that was distributed with this source code.
 */

namespace Eloquent\Phony\Stub\Answer\Builder;

use Eloquent\Phony\Stub\Answer\CallRequest;

/**
 * Represents an iteration of a generator that ends in a yield.
 *
 * @codeCoverageIgnore
 */
class GeneratorYieldIteration
{
    /**
     * Construct a new generator yield iteration.
     *
     * @param array<CallRequest> $requests The requests.
     * @param bool               $hasKey   True if the key should be yielded.
     * @param mixed              $key      The key.
     * @param bool               $hasValue True if the value should be yielded.
     * @param mixed              $value    The value.
     */
    public function __construct(
        array $requests,
        $hasKey,
        $key,
        $hasValue,
        $value
    ) {
        $this->requests = $requests;
        $this->hasKey = $hasKey;
        $this->key = $key;
        $this->hasValue = $hasValue;
        $this->value = $value;
    }

    public $requests;
    public $hasKey;
    public $key;
    public $hasValue;
    public $value;
}

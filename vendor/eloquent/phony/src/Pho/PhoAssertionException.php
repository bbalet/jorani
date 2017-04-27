<?php

/*
 * This file is part of the Phony package.
 *
 * Copyright © 2016 Erin Millard
 *
 * For the full copyright and license information, please view the LICENSE file
 * that was distributed with this source code.
 */

namespace Eloquent\Phony\Pho;

use pho\Exception\ExpectationException;

/**
 * Emulates Pho's expectation exception for improved assertion failure output.
 */
final class PhoAssertionException extends ExpectationException
{
    /**
     * Construct a new Pho assertion exception.
     *
     * @param string $description The failure description.
     */
    public function __construct($description)
    {
        parent::__construct($description);

        foreach ($this->getTrace() as $call) {
            if (!isset($call['class'])) {
                continue;
            }

            if (0 !== strpos($call['class'], 'Eloquent\Phony\\')) {
                break;
            }

            if (isset($call['file']) && isset($call['line'])) {
                $this->file = $call['file'];
                $this->line = $call['line'];
            }
        }
    }
}

<?php

/*
 * This file is part of the Phony package.
 *
 * Copyright © 2016 Erin Millard
 *
 * For the full copyright and license information, please view the LICENSE file
 * that was distributed with this source code.
 */

namespace Eloquent\Phony\Verification\Exception;

use Exception;

/**
 * The requested operation would create an invalid cardinality state.
 */
final class InvalidCardinalityStateException extends Exception implements
    InvalidCardinalityException
{
    /**
     * Construct a new invalid cardinality state exception.
     */
    public function __construct()
    {
        parent::__construct('Invalid cardinality.');
    }
}

<?php

/*
 * This file is part of the Phony package.
 *
 * Copyright © 2016 Erin Millard
 *
 * For the full copyright and license information, please view the LICENSE file
 * that was distributed with this source code.
 */

namespace Eloquent\Phony\Mock\Exception;

use Exception;

/**
 * Unable to modify a finalized mock.
 */
final class FinalizedMockException extends Exception implements
    MockException
{
    /**
     * Construct a finalized mock exception.
     */
    public function __construct()
    {
        parent::__construct('Unable to modify a finalized mock.');
    }
}

<?php

/*
 * This file is part of the Phony package.
 *
 * Copyright © 2016 Erin Millard
 *
 * For the full copyright and license information, please view the LICENSE file
 * that was distributed with this source code.
 */

namespace Eloquent\Phony\Stub\Exception;

use Exception;

/**
 * No answer was defined, or the answer is incomplete.
 */
final class UndefinedAnswerException extends Exception
{
    /**
     * Construct a new undefined answer exception.
     */
    public function __construct()
    {
        parent::__construct(
            'No answer was defined, or the answer is incomplete.'
        );
    }
}

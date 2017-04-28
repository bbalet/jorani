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
 * Anonymous classes cannot be mocked.
 */
final class AnonymousClassException extends Exception implements
    MockException
{
    /**
     * Construct an anonymous class exception.
     */
    public function __construct()
    {
        parent::__construct('Anonymous classes cannot be mocked.');
    }
}

<?php

/*
 * This file is part of the Phony package.
 *
 * Copyright © 2016 Erin Millard
 *
 * For the full copyright and license information, please view the LICENSE file
 * that was distributed with this source code.
 */

namespace Eloquent\Phony\Call\Exception;

use Exception;

/**
 * An undefined call was requested.
 */
final class UndefinedCallException extends Exception
{
    /**
     * Construct a new undefined call exception.
     *
     * @param int $index The call index.
     */
    public function __construct($index)
    {
        $this->index = $index;

        parent::__construct(
            sprintf('No call defined for index %s.', var_export($index, true))
        );
    }

    /**
     * Get the call index.
     *
     * @return int The call index.
     */
    public function index()
    {
        return $this->index;
    }

    private $index;
}

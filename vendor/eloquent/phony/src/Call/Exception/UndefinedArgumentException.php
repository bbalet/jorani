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
 * Thrown when an argument that was requested by index does not exist.
 */
final class UndefinedArgumentException extends Exception
{
    /**
     * Construct a new undefined argument exception.
     *
     * @param int $index The index.
     */
    public function __construct($index)
    {
        $this->index = $index;

        parent::__construct(
            sprintf(
                'No argument defined for index %s.',
                var_export($index, true)
            )
        );
    }

    /**
     * Get the index.
     *
     * @return int The index.
     */
    public function index()
    {
        return $this->index;
    }

    private $index;
}

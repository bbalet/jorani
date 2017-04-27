<?php

/*
 * This file is part of the Phony package.
 *
 * Copyright © 2016 Erin Millard
 *
 * For the full copyright and license information, please view the LICENSE file
 * that was distributed with this source code.
 */

namespace Eloquent\Phony\Event\Exception;

use Exception;

/**
 * No event is defined for the requested index.
 */
final class UndefinedEventException extends Exception
{
    /**
     * Construct a new undefined event exception.
     *
     * @param int            $index The index.
     * @param Exception|null $cause The cause, if available.
     */
    public function __construct($index, Exception $cause = null)
    {
        $this->index = $index;

        parent::__construct(
            sprintf('No event defined for index %d.', $index),
            0,
            $cause
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

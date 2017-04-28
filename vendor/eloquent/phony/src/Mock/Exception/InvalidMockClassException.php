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
 * The supplied value is not a mock class.
 */
final class InvalidMockClassException extends Exception implements
    MockException
{
    /**
     * Construct a new invalid mock class exception.
     *
     * @param mixed $value The value.
     */
    public function __construct($value)
    {
        $this->value = $value;

        parent::__construct(
            sprintf(
                'Value of type %s is not a mock class.',
                var_export(gettype($value), true)
            )
        );
    }

    /**
     * Get the value.
     *
     * @return mixed The value.
     */
    public function value()
    {
        return $this->value;
    }

    private $value;
}

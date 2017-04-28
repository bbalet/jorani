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
 * The supplied value is not a mock.
 */
final class InvalidMockException extends Exception implements
    MockException
{
    /**
     * Construct a new invalid mock exception.
     *
     * @param mixed $value The value.
     */
    public function __construct($value)
    {
        $this->value = $value;

        if (is_object($value)) {
            $message = sprintf(
                'Object of type %s is not a mock.',
                var_export(get_class($value), true)
            );
        } else {
            $message = sprintf(
                'Value of type %s is not a mock.',
                var_export(gettype($value), true)
            );
        }

        parent::__construct($message);
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

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
 * The supplied class is not a mock class.
 */
final class NonMockClassException extends Exception implements
    MockException
{
    /**
     * Construct a non-mock class exception.
     *
     * @param string         $className The class name.
     * @param Exception|null $cause     The cause, if available.
     */
    public function __construct($className, Exception $cause = null)
    {
        $this->className = $className;

        parent::__construct(
            sprintf(
                'The class %s is not a mock class.',
                var_export($className, true)
            ),
            0,
            $cause
        );
    }

    /**
     * Get the class name.
     *
     * @return string The class name.
     */
    public function className()
    {
        return $this->className;
    }

    private $className;
}

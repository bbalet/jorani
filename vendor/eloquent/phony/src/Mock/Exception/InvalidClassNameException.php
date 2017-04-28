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
 * The supplied class name is invalid.
 */
final class InvalidClassNameException extends Exception implements
    MockException
{
    /**
     * Construct a new invalid class name exception.
     *
     * @param mixed $className The class name.
     */
    public function __construct($className)
    {
        $this->className = $className;

        parent::__construct(
            sprintf('Invalid class name %s.', var_export($className, true))
        );
    }

    /**
     * Get the class name.
     *
     * @return mixed The class name.
     */
    public function className()
    {
        return $this->className;
    }

    private $className;
}

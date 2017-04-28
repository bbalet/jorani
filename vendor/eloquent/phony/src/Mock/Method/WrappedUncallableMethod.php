<?php

/*
 * This file is part of the Phony package.
 *
 * Copyright © 2016 Erin Millard
 *
 * For the full copyright and license information, please view the LICENSE file
 * that was distributed with this source code.
 */

namespace Eloquent\Phony\Mock\Method;

use Eloquent\Phony\Mock\Handle\Handle;
use Error;
use Exception;
use ReflectionMethod;

/**
 * A wrapper for uncallable methods.
 */
class WrappedUncallableMethod extends AbstractWrappedMethod
{
    /**
     * Construct a new wrapped uncallable method.
     *
     * @param ReflectionMethod $method      The method.
     * @param Handle           $handle      The handle.
     * @param mixed            $returnValue The return value.
     */
    public function __construct(
        ReflectionMethod $method,
        Handle $handle,
        $returnValue
    ) {
        $this->returnValue = $returnValue;

        parent::__construct($method, $handle);
    }

    /**
     * Invoke this object.
     *
     * This method supports reference parameters.
     *
     * @param Arguments|array $arguments The arguments.
     *
     * @return mixed           The result of invocation.
     * @throws Exception|Error If an error occurs.
     */
    public function invokeWith($arguments = array())
    {
        return $this->returnValue;
    }

    private $returnValue;
}

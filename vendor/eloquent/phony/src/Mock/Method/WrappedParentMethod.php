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

use Eloquent\Phony\Call\Arguments;
use Eloquent\Phony\Mock\Handle\Handle;
use Error;
use Exception;
use ReflectionMethod;

/**
 * A wrapper that allows calling of the parent method in mocks.
 */
class WrappedParentMethod extends AbstractWrappedMethod
{
    /**
     * Construct a new wrapped parent method.
     *
     * @param ReflectionMethod $callParentMethod The _callParent() method.
     * @param ReflectionMethod $method           The method.
     * @param Handle           $handle           The handle.
     */
    public function __construct(
        ReflectionMethod $callParentMethod,
        ReflectionMethod $method,
        Handle $handle
    ) {
        $this->callParentMethod = $callParentMethod;

        parent::__construct($method, $handle);
    }

    /**
     * Get the _callParent() method.
     *
     * @return ReflectionMethod The _callParent() method.
     */
    public function callParentMethod()
    {
        return $this->callParentMethod;
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
        if (!$arguments instanceof Arguments) {
            $arguments = new Arguments($arguments);
        }

        return $this->callParentMethod
            ->invoke($this->mock, $this->name, $arguments);
    }

    private $callParentMethod;
}

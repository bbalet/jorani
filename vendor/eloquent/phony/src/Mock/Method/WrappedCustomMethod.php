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
use Eloquent\Phony\Invocation\Invoker;
use Eloquent\Phony\Mock\Handle\Handle;
use Error;
use Exception;
use ReflectionMethod;

/**
 * A wrapper for custom methods.
 */
class WrappedCustomMethod extends AbstractWrappedMethod
{
    /**
     * Construct a new wrapped custom method.
     *
     * @param callable         $customCallback The custom callback.
     * @param ReflectionMethod $method         The method.
     * @param Handle           $handle         The handle.
     * @param Invoker          $invoker        The invoker to use.
     */
    public function __construct(
        $customCallback,
        ReflectionMethod $method,
        Handle $handle,
        Invoker $invoker
    ) {
        $this->customCallback = $customCallback;
        $this->invoker = $invoker;

        parent::__construct($method, $handle);
    }

    /**
     * Get the custom callback.
     *
     * @return ReflectionMethod The custom callback.
     */
    public function customCallback()
    {
        return $this->customCallback;
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

        return $this->invoker->callWith($this->customCallback, $arguments);
    }

    private $customCallback;
    private $invoker;
}

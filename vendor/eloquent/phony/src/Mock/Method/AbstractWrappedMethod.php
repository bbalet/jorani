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

use Eloquent\Phony\Invocation\AbstractWrappedInvocable;
use Eloquent\Phony\Mock\Handle\Handle;
use Eloquent\Phony\Mock\Handle\StaticHandle;
use Eloquent\Phony\Mock\Mock;
use ReflectionMethod;

/**
 * An abstract base class for implementing wrapped methods.
 */
abstract class AbstractWrappedMethod extends AbstractWrappedInvocable implements
    WrappedMethod
{
    /**
     * Construct a new wrapped method.
     *
     * @param ReflectionMethod $method The method.
     * @param Handle           $handle The handle.
     */
    public function __construct(ReflectionMethod $method, Handle $handle)
    {
        $this->method = $method;
        $this->handle = $handle;
        $this->name = $method->getName();

        if ($handle instanceof StaticHandle) {
            $this->mock = null;
            $callback = array(
                $method->getDeclaringClass()->getName(),
                $this->name,
            );
        } else {
            $this->mock = $handle->get();
            $callback = array($this->mock, $this->name);
        }

        parent::__construct($callback, null);
    }

    /**
     * Get the method.
     *
     * @return ReflectionMethod The method.
     */
    public function method()
    {
        return $this->method;
    }

    /**
     * Get the name.
     *
     * @return string The name.
     */
    public function name()
    {
        return $this->name;
    }

    /**
     * Get the handle.
     *
     * @return Handle The handle.
     */
    public function handle()
    {
        return $this->handle;
    }

    /**
     * Get the mock.
     *
     * @return Mock|null The mock.
     */
    public function mock()
    {
        return $this->mock;
    }

    protected $method;
    protected $handle;
    protected $mock;
    protected $name;
}

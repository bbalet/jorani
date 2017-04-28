<?php

/*
 * This file is part of the Phony package.
 *
 * Copyright © 2016 Erin Millard
 *
 * For the full copyright and license information, please view the LICENSE file
 * that was distributed with this source code.
 */

namespace Eloquent\Phony\Invocation;

use Closure;
use ReflectionClass;
use ReflectionException;
use ReflectionFunction;
use ReflectionFunctionAbstract;
use ReflectionMethod;
use ReflectionType;

/**
 * Utilities for inspecting invocables.
 */
class InvocableInspector
{
    /**
     * Get the static instance of this inspector.
     *
     * @return InvocableInspector The static inspector.
     */
    public static function instance()
    {
        if (!self::$instance) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * Construct a new invocable inspector.
     */
    public function __construct()
    {
        $reflectorReflector = new ReflectionClass('ReflectionFunction');
        $this->isBoundClosureSupported =
            $reflectorReflector->hasMethod('getClosureThis');
        $this->isReturnTypeSupported =
            $reflectorReflector->hasMethod('getReturnType');
    }

    /**
     * Get the appropriate reflector for the supplied callback.
     *
     * @param callable $callback The callback.
     *
     * @return ReflectionFunctionAbstract The reflector.
     * @throws ReflectionException        If the callback cannot be reflected.
     */
    public function callbackReflector($callback)
    {
        while ($callback instanceof WrappedInvocable) {
            $callback = $callback->callback();
        }

        if (is_array($callback)) {
            return new ReflectionMethod($callback[0], $callback[1]);
        }

        if (is_string($callback) && false !== strpos($callback, '::')) {
            list($className, $methodName) = explode('::', $callback);

            return new ReflectionMethod($className, $methodName);
        }

        if (is_object($callback) && !$callback instanceof Closure) {
            if (method_exists($callback, '__invoke')) {
                return new ReflectionMethod($callback, '__invoke');
            }

            throw new ReflectionException('Invalid callback.');
        }

        return new ReflectionFunction($callback);
    }

    /**
     * Get the return type for the supplied callback.
     *
     * @param callable $callback The callback.
     *
     * @return ReflectionType|null The return type, or null if no return type is defined.
     */
    public function callbackReturnType($callback)
    {
        if (!$this->isReturnTypeSupported) {
            return null; // @codeCoverageIgnore
        }

        return $this->callbackReflector($callback)->getReturnType();
    }

    /**
     * Returns true if bound closures are supported.
     *
     * @return bool True if bound closures are supported.
     */
    public function isBoundClosureSupported()
    {
        return $this->isBoundClosureSupported;
    }

    private static $instance;
    private $isBoundClosureSupported;
    private $isReturnTypeSupported;
}

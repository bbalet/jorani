<?php

/*
 * This file is part of the Phony package.
 *
 * Copyright © 2016 Erin Millard
 *
 * For the full copyright and license information, please view the LICENSE file
 * that was distributed with this source code.
 */

namespace Eloquent\Phony\Mock\Builder\Method;

use ReflectionFunctionAbstract;
use ReflectionMethod;

/**
 * Represents a real method definition.
 */
class RealMethodDefinition implements MethodDefinition
{
    /**
     * Construct a new real method definition.
     *
     * @param ReflectionMethod $method The method.
     * @param string           $name   The name.
     */
    public function __construct(ReflectionMethod $method, $name)
    {
        $this->method = $method;
        $this->name = $name;
        $this->isCallable = !$this->method->isAbstract();
        $this->isStatic = $this->method->isStatic();

        if ($this->method->isPublic()) {
            $this->accessLevel = 'public';
        } else {
            $this->accessLevel = 'protected';
        }
    }

    /**
     * Returns true if this method is callable.
     *
     * @return bool True if this method is callable.
     */
    public function isCallable()
    {
        return $this->isCallable;
    }

    /**
     * Returns true if this method is static.
     *
     * @return bool True if this method is static.
     */
    public function isStatic()
    {
        return $this->isStatic;
    }

    /**
     * Returns true if this method is custom.
     *
     * @return bool True if this method is custom.
     */
    public function isCustom()
    {
        return false;
    }

    /**
     * Get the access level.
     *
     * @return string The access level.
     */
    public function accessLevel()
    {
        return $this->accessLevel;
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
     * Get the method.
     *
     * @return ReflectionFunctionAbstract The method.
     */
    public function method()
    {
        return $this->method;
    }

    /**
     * Get the callback.
     *
     * @return callable|null The callback, or null if this is a real method.
     */
    public function callback()
    {
        return;
    }

    private $method;
    private $name;
    private $isCallable;
    private $isStatic;
    private $accessLevel;
}

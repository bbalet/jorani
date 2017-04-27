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

use ReflectionMethod;

/**
 * Represents a collection of methods.
 */
class MethodDefinitionCollection
{
    /**
     * Construct a new custom method definition.
     *
     * @param array<string,MethodDefinition> $methods      The methods.
     * @param array<TraitMethodDefinition>   $traitMethods The trait methods.
     */
    public function __construct(array $methods, array $traitMethods)
    {
        $this->methodNames = array();
        $this->allMethods = $methods;
        $this->traitMethods = $traitMethods;
        $this->staticMethods = array();
        $this->methods = array();
        $this->publicStaticMethods = array();
        $this->publicMethods = array();
        $this->protectedStaticMethods = array();
        $this->protectedMethods = array();

        foreach ($methods as $name => $method) {
            $this->methodNames[strtolower($name)] = $name;

            $isStatic = $method->isStatic();
            $accessLevel = $method->accessLevel();
            $isPublic = 'public' === $accessLevel;

            if ($isStatic) {
                $this->staticMethods[$name] = $method;

                if ($isPublic) {
                    $this->publicStaticMethods[$name] = $method;
                } else {
                    $this->protectedStaticMethods[$name] = $method;
                }
            } else {
                $this->methods[$name] = $method;

                if ($isPublic) {
                    $this->publicMethods[$name] = $method;
                } else {
                    $this->protectedMethods[$name] = $method;
                }
            }
        }
    }

    /**
     * Get the canonical method name for the supplied method name.
     *
     * @param string $name The method name.
     *
     * @return string|null The canonical method name, or null if no such method exists.
     */
    public function methodName($name)
    {
        $name = strtolower($name);

        if (isset($this->methodNames[$name])) {
            return $this->methodNames[$name];
        }

        return null;
    }

    /**
     * Get the methods.
     *
     * @return array<string,MethodDefinition> The methods.
     */
    public function allMethods()
    {
        return $this->allMethods;
    }

    /**
     * Get the static methods.
     *
     * @return array<string,MethodDefinition> The methods.
     */
    public function staticMethods()
    {
        return $this->staticMethods;
    }

    /**
     * Get the instance methods.
     *
     * @return array<string,MethodDefinition> The methods.
     */
    public function methods()
    {
        return $this->methods;
    }

    /**
     * Get the public static methods.
     *
     * @return array<string,MethodDefinition> The methods.
     */
    public function publicStaticMethods()
    {
        return $this->publicStaticMethods;
    }

    /**
     * Get the public instance methods.
     *
     * @return array<string,MethodDefinition> The methods.
     */
    public function publicMethods()
    {
        return $this->publicMethods;
    }

    /**
     * Get the protected static methods.
     *
     * @return array<string,MethodDefinition> The methods.
     */
    public function protectedStaticMethods()
    {
        return $this->protectedStaticMethods;
    }

    /**
     * Get the protected instance methods.
     *
     * @return array<string,MethodDefinition> The methods.
     */
    public function protectedMethods()
    {
        return $this->protectedMethods;
    }

    /**
     * Get the trait methods.
     *
     * @return array<ReflectionMethod> The trait methods.
     */
    public function traitMethods()
    {
        return $this->traitMethods;
    }

    private $methodNames;
    private $allMethods;
    private $traitMethods;
    private $staticMethods;
    private $methods;
    private $publicStaticMethods;
    private $publicMethods;
    private $protectedStaticMethods;
    private $protectedMethods;
}

<?php

/*
 * This file is part of the Phony package.
 *
 * Copyright © 2016 Erin Millard
 *
 * For the full copyright and license information, please view the LICENSE file
 * that was distributed with this source code.
 */

namespace Eloquent\Phony\Mock\Handle;

use Eloquent\Phony\Assertion\AssertionRecorder;
use Eloquent\Phony\Assertion\AssertionRenderer;
use Eloquent\Phony\Call\Arguments;
use Eloquent\Phony\Invocation\Invoker;
use Eloquent\Phony\Mock\Mock;
use Eloquent\Phony\Stub\EmptyValueFactory;
use Eloquent\Phony\Stub\StubFactory;
use Eloquent\Phony\Stub\StubVerifierFactory;
use ReflectionClass;
use ReflectionObject;
use stdClass;

/**
 * A handle for stubbing and verifying a mock instance.
 */
class InstanceHandle extends AbstractHandle
{
    /**
     * Construct a new instance handle.
     *
     * @param Mock                $mock                The mock.
     * @param stdClass            $state               The state.
     * @param StubFactory         $stubFactory         The stub factory to use.
     * @param StubVerifierFactory $stubVerifierFactory The stub verifier factory to use.
     * @param EmptyValueFactory   $emptyValueFactory   The empty value factory to use.
     * @param AssertionRenderer   $assertionRenderer   The assertion renderer to use.
     * @param AssertionRecorder   $assertionRecorder   The assertion recorder to use.
     * @param Invoker             $invoker             The invoker to use.
     */
    public function __construct(
        Mock $mock,
        stdClass $state,
        StubFactory $stubFactory,
        StubVerifierFactory $stubVerifierFactory,
        EmptyValueFactory $emptyValueFactory,
        AssertionRenderer $assertionRenderer,
        AssertionRecorder $assertionRecorder,
        Invoker $invoker
    ) {
        $class = new ReflectionClass($mock);

        if ($class->hasMethod('_callParent')) {
            $callParentMethod = $class->getMethod('_callParent');
            $callParentMethod->setAccessible(true);
        } else {
            $callParentMethod = null;
        }

        if ($class->hasMethod('_callParentConstructor')) {
            $callParentConstructorMethod =
                $class->getMethod('_callParentConstructor');
            $callParentConstructorMethod->setAccessible(true);
        } else {
            $callParentConstructorMethod = null;
        }

        if ($class->hasMethod('_callTrait')) {
            $callTraitMethod = $class->getMethod('_callTrait');
            $callTraitMethod->setAccessible(true);
        } else {
            $callTraitMethod = null;
        }

        if ($class->hasMethod('_callMagic')) {
            $callMagicMethod = $class->getMethod('_callMagic');
            $callMagicMethod->setAccessible(true);
        } else {
            $callMagicMethod = null;
        }

        $this->callParentConstructorMethod = $callParentConstructorMethod;

        parent::__construct(
            $class,
            $state,
            $callParentMethod,
            $callTraitMethod,
            $callMagicMethod,
            $mock,
            $stubFactory,
            $stubVerifierFactory,
            $emptyValueFactory,
            $assertionRenderer,
            $assertionRecorder,
            $invoker
        );
    }

    /**
     * Get the mock.
     *
     * @return Mock The mock.
     */
    public function get()
    {
        return $this->mock;
    }

    /**
     * Call the original constructor.
     *
     * @param mixed ...$arguments The arguments.
     *
     * @return $this This handle.
     */
    public function construct()
    {
        return $this->constructWith(func_get_args());
    }

    /**
     * Call the original constructor.
     *
     * @param Arguments|array $arguments The arguments.
     *
     * @return $this This handle.
     */
    public function constructWith($arguments = array())
    {
        if ($this->callParentConstructorMethod) {
            if (!$arguments instanceof Arguments) {
                $arguments = new Arguments($arguments);
            }

            $this->callParentConstructorMethod->invoke($this->mock, $arguments);
        }

        return $this;
    }

    /**
     * Set the label.
     *
     * @param string|null $label The label.
     *
     * @return $this This handle.
     */
    public function setLabel($label)
    {
        $this->state->label = $label;

        return $this;
    }

    /**
     * Get the label.
     *
     * @return string|null The label.
     */
    public function label()
    {
        return $this->state->label;
    }

    /**
     * Use the supplied object as the implementation for all methods of the
     * mock.
     *
     * This method may help when partial mocking of a particular implementation
     * is not possible; as in the case of a final class.
     *
     * @param object $object The object to use.
     *
     * @return $this This handle.
     */
    public function proxy($object)
    {
        $reflector = new ReflectionObject($object);

        foreach ($reflector->getMethods() as $method) {
            if (
                $method->isStatic() ||
                $method->isPrivate() ||
                $method->isConstructor() ||
                $method->isDestructor()
            ) {
                continue;
            }

            $name = $method->getName();

            if ($this->class->hasMethod($name)) {
                $method->setAccessible(true);

                $this->stub($name)->doesWith(
                    function ($arguments) use ($method, $object) {
                        return $method->invokeArgs($object, $arguments->all());
                    },
                    array(),
                    false,
                    true,
                    false
                );
            }
        }

        return $this;
    }

    private $callParentConstructorMethod;
}

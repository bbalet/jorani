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
use Eloquent\Phony\Invocation\Invoker;
use Eloquent\Phony\Stub\EmptyValueFactory;
use Eloquent\Phony\Stub\StubFactory;
use Eloquent\Phony\Stub\StubVerifierFactory;
use ReflectionClass;
use ReflectionObject;
use stdClass;

/**
 * A handle for stubbing and verifying a mock class.
 */
class StaticHandle extends AbstractHandle
{
    /**
     * Construct a new static handle.
     *
     * @param ReflectionClass     $class               The class.
     * @param stdClass            $state               The state.
     * @param StubFactory         $stubFactory         The stub factory to use.
     * @param StubVerifierFactory $stubVerifierFactory The stub verifier factory to use.
     * @param EmptyValueFactory   $emptyValueFactory   The empty value factory to use.
     * @param AssertionRenderer   $assertionRenderer   The assertion renderer to use.
     * @param AssertionRecorder   $assertionRecorder   The assertion recorder to use.
     * @param Invoker             $invoker             The invoker to use.
     */
    public function __construct(
        ReflectionClass $class,
        stdClass $state,
        StubFactory $stubFactory,
        StubVerifierFactory $stubVerifierFactory,
        EmptyValueFactory $emptyValueFactory,
        AssertionRenderer $assertionRenderer,
        AssertionRecorder $assertionRecorder,
        Invoker $invoker
    ) {
        if ($class->hasMethod('_callParentStatic')) {
            $callParentMethod = $class->getMethod('_callParentStatic');
            $callParentMethod->setAccessible(true);
        } else {
            $callParentMethod = null;
        }

        if ($class->hasMethod('_callTraitStatic')) {
            $callTraitMethod = $class->getMethod('_callTraitStatic');
            $callTraitMethod->setAccessible(true);
        } else {
            $callTraitMethod = null;
        }

        if ($class->hasMethod('_callMagicStatic')) {
            $callMagicMethod = $class->getMethod('_callMagicStatic');
            $callMagicMethod->setAccessible(true);
        } else {
            $callMagicMethod = null;
        }

        parent::__construct(
            $class,
            $state,
            $callParentMethod,
            $callTraitMethod,
            $callMagicMethod,
            null,
            $stubFactory,
            $stubVerifierFactory,
            $emptyValueFactory,
            $assertionRenderer,
            $assertionRecorder,
            $invoker
        );
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
            if (!$method->isStatic() || $method->isPrivate()) {
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
}

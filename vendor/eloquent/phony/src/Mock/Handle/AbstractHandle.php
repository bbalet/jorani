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
use Eloquent\Phony\Event\EventCollection;
use Eloquent\Phony\Invocation\Invoker;
use Eloquent\Phony\Mock\Exception\FinalMethodStubException;
use Eloquent\Phony\Mock\Exception\UndefinedMethodStubException;
use Eloquent\Phony\Mock\Method\WrappedCustomMethod;
use Eloquent\Phony\Mock\Method\WrappedMagicMethod;
use Eloquent\Phony\Mock\Method\WrappedParentMethod;
use Eloquent\Phony\Mock\Method\WrappedTraitMethod;
use Eloquent\Phony\Mock\Method\WrappedUncallableMethod;
use Eloquent\Phony\Mock\Mock;
use Eloquent\Phony\Spy\Spy;
use Eloquent\Phony\Stub\EmptyValueFactory;
use Eloquent\Phony\Stub\StubFactory;
use Eloquent\Phony\Stub\StubVerifier;
use Eloquent\Phony\Stub\StubVerifierFactory;
use ReflectionClass;
use ReflectionMethod;
use stdClass;

/**
 * An abstract base class for implementing handles.
 */
abstract class AbstractHandle implements Handle
{
    /**
     * Construct a new handle.
     *
     * @param ReflectionClass       $class               The class.
     * @param stdClass              $state               The state.
     * @param ReflectionMethod|null $callParentMethod    The call parent method, or null if no parent class exists.
     * @param ReflectionMethod|null $callTraitMethod     The call trait method, or null if no trait methods are implemented.
     * @param ReflectionMethod|null $callMagicMethod     The call magic method, or null if magic calls are not supported.
     * @param Mock|null             $mock                The mock, or null if this is a static handle.
     * @param StubFactory           $stubFactory         The stub factory to use.
     * @param StubVerifierFactory   $stubVerifierFactory The stub verifier factory to use.
     * @param EmptyValueFactory     $emptyValueFactory   The empty value factory to use.
     * @param AssertionRenderer     $assertionRenderer   The assertion renderer to use.
     * @param AssertionRecorder     $assertionRecorder   The assertion recorder to use.
     * @param Invoker               $invoker             The invoker to use.
     */
    public function __construct(
        ReflectionClass $class,
        stdClass $state,
        ReflectionMethod $callParentMethod = null,
        ReflectionMethod $callTraitMethod = null,
        ReflectionMethod $callMagicMethod = null,
        Mock $mock = null,
        StubFactory $stubFactory,
        StubVerifierFactory $stubVerifierFactory,
        EmptyValueFactory $emptyValueFactory,
        AssertionRenderer $assertionRenderer,
        AssertionRecorder $assertionRecorder,
        Invoker $invoker
    ) {
        $this->mock = $mock;
        $this->class = $class;
        $this->state = $state;
        $this->callParentMethod = $callParentMethod;
        $this->callTraitMethod = $callTraitMethod;
        $this->callMagicMethod = $callMagicMethod;
        $this->stubFactory = $stubFactory;
        $this->stubVerifierFactory = $stubVerifierFactory;
        $this->emptyValueFactory = $emptyValueFactory;
        $this->assertionRenderer = $assertionRenderer;
        $this->assertionRecorder = $assertionRecorder;
        $this->invoker = $invoker;

        $uncallableMethodsProperty = $class->getProperty('_uncallableMethods');
        $uncallableMethodsProperty->setAccessible(true);
        $this->uncallableMethods = $uncallableMethodsProperty->getValue(null);

        $traitMethodsProperty = $class->getProperty('_traitMethods');
        $traitMethodsProperty->setAccessible(true);
        $this->traitMethods = $traitMethodsProperty->getValue(null);

        $customMethodsProperty = $class->getProperty('_customMethods');
        $customMethodsProperty->setAccessible(true);
        $this->customMethods = $customMethodsProperty->getValue(null);
    }

    /**
     * Get the class.
     *
     * @return ReflectionClass The class.
     */
    public function clazz()
    {
        return $this->class;
    }

    /**
     * Get the class name.
     *
     * @return string The class name.
     */
    public function className()
    {
        return $this->class->getName();
    }

    /**
     * Turn the mock into a full mock.
     *
     * @return $this This handle.
     */
    public function full()
    {
        $this->state->defaultAnswerCallback =
            'Eloquent\Phony\Stub\StubData::returnsEmptyAnswerCallback';

        return $this;
    }

    /**
     * Turn the mock into a partial mock.
     *
     * @return $this This handle.
     */
    public function partial()
    {
        $this->state->defaultAnswerCallback =
            'Eloquent\Phony\Stub\StubData::forwardsAnswerCallback';

        return $this;
    }

    /**
     * Set the callback to use when creating a default answer.
     *
     * @param callable $defaultAnswerCallback The default answer callback.
     *
     * @return $this This handle.
     */
    public function setDefaultAnswerCallback($defaultAnswerCallback)
    {
        $this->state->defaultAnswerCallback = $defaultAnswerCallback;

        return $this;
    }

    /**
     * Get the default answer callback.
     *
     * @return callable The default answer callback.
     */
    public function defaultAnswerCallback()
    {
        return $this->state->defaultAnswerCallback;
    }

    /**
     * Get a stub verifier.
     *
     * @param string $name      The method name.
     * @param bool   $isNewRule True if a new rule should be started.
     *
     * @return StubVerifier  The stub verifier.
     * @throws MockException If the stub does not exist.
     */
    public function stub($name, $isNewRule = true)
    {
        $key = strtolower($name);

        if (isset($this->state->stubs->$key)) {
            $stub = $this->state->stubs->$key;
        } else {
            $stub = $this->state->stubs->$key = $this->createStub($name);
        }

        if ($isNewRule) {
            $stub->closeRule();
        }

        return $stub;
    }

    /**
     * Get a stub verifier.
     *
     * Using this method will always start a new rule.
     *
     * @param string $name The method name.
     *
     * @return StubVerifier  The stub verifier.
     * @throws MockException If the stub does not exist.
     */
    public function __get($name)
    {
        $key = strtolower($name);

        if (isset($this->state->stubs->$key)) {
            $stub = $this->state->stubs->$key;
        } else {
            $stub = $this->state->stubs->$key = $this->createStub($name);
        }

        return $stub->closeRule();
    }

    /**
     * Get a spy.
     *
     * @param string $name The method name.
     *
     * @return Spy           The spy.
     * @throws MockException If the spy does not exist.
     */
    public function spy($name)
    {
        return $this->stub($name)->spy();
    }

    /**
     * Checks if there was no interaction with the mock.
     *
     * @return EventCollection|null The result.
     */
    public function checkNoInteraction()
    {
        foreach (get_object_vars($this->state->stubs) as $stub) {
            if ($stub->checkCalled()) {
                return;
            }
        }

        return $this->assertionRecorder->createSuccess();
    }

    /**
     * Throws an exception unless there was no interaction with the mock.
     *
     * @return EventCollection The result.
     * @throws Exception       If the assertion fails, and the assertion recorder throws exceptions.
     */
    public function noInteraction()
    {
        if ($result = $this->checkNoInteraction()) {
            return $result;
        }

        $calls = array();

        foreach (get_object_vars($this->state->stubs) as $stub) {
            $calls = array_merge($calls, $stub->allCalls());
        }

        return $this->assertionRecorder->createFailure(
            $this->assertionRenderer->renderNoInteraction($this, $calls)
        );
    }

    /**
     * Stop recording calls.
     *
     * @return $this This handle.
     */
    public function stopRecording()
    {
        foreach (get_object_vars($this->state->stubs) as $stub) {
            $stub->stopRecording();
        }

        $this->state->isRecording = false;

        return $this;
    }

    /**
     * Start recording calls.
     *
     * @return $this This handle.
     */
    public function startRecording()
    {
        foreach (get_object_vars($this->state->stubs) as $stub) {
            $stub->startRecording();
        }

        $this->state->isRecording = true;

        return $this;
    }

    /**
     * Get the handle state.
     *
     * @return stdClass The state.
     */
    public function state()
    {
        return $this->state;
    }

    /**
     * Create a new stub verifier.
     *
     * @param string $name The method name.
     *
     * @return StubVerifier  The stub verifier.
     * @throws MockException If the method does not exist.
     */
    protected function createStub($name)
    {
        $isMagic = !$this->class->hasMethod($name);
        $callMagicMethod = $this->callMagicMethod;

        if ($isMagic && !$callMagicMethod) {
            throw new UndefinedMethodStubException(
                $this->class->getName(),
                $name
            );
        }

        $mock = $this->mock;
        $key = strtolower($name);

        if ($isMagic) {
            if ($mock) {
                $magicKey = '__call';
            } else {
                $magicKey = '__callstatic';
            }

            if (isset($this->uncallableMethods[$magicKey])) {
                $isUncallable = true;
                $returnValue = $this->emptyValueFactory->fromFunction(
                    $this->class->getMethod($magicKey)
                );
            } else {
                $isUncallable = false;
                $returnValue = null;
            }

            $stub = $this->stubFactory->create(
                new WrappedMagicMethod(
                    $name,
                    $this->callMagicMethod,
                    $isUncallable,
                    $this,
                    $returnValue
                ),
                $mock,
                $this->state->defaultAnswerCallback
            );
        } elseif (isset($this->uncallableMethods[$key])) {
            $method = $this->class->getMethod($name);
            $stub = $this->stubFactory->create(
                new WrappedUncallableMethod(
                    $method,
                    $this,
                    $this->emptyValueFactory->fromFunction($method)
                ),
                $mock,
                $this->state->defaultAnswerCallback
            );
        } elseif (isset($this->traitMethods[$key])) {
            $stub = $this->stubFactory->create(
                new WrappedTraitMethod(
                    $this->callTraitMethod,
                    $this->traitMethods[$key],
                    $this->class->getMethod($name),
                    $this
                ),
                $mock,
                $this->state->defaultAnswerCallback
            );
        } elseif (array_key_exists($key, $this->customMethods)) {
            $stub = $this->stubFactory->create(
                new WrappedCustomMethod(
                    $this->customMethods[$key],
                    $this->class->getMethod($name),
                    $this,
                    $this->invoker
                ),
                $mock,
                $this->state->defaultAnswerCallback
            );
        } else {
            $method = $this->class->getMethod($name);

            if ($method->isFinal()) {
                throw new FinalMethodStubException(
                    $this->class->getName(),
                    $name
                );
            }

            $stub = $this->stubFactory->create(
                new WrappedParentMethod($this->callParentMethod, $method, $this),
                $mock,
                $this->state->defaultAnswerCallback
            );
        }

        $stubVerifier = $this->stubVerifierFactory->create($stub);

        if (!$this->state->isRecording) {
            $stubVerifier->stopRecording();
        }

        return $stubVerifier;
    }

    protected $state;
    protected $class;
    protected $mock;
    private $uncallableMethods;
    private $traitMethods;
    private $callParentMethod;
    private $callTraitMethod;
    private $callMagicMethod;
    private $stubFactory;
    private $stubVerifierFactory;
    private $emptyValueFactory;
    private $assertionRenderer;
    private $assertionRecorder;
    private $invoker;
    private $customMethods;
}

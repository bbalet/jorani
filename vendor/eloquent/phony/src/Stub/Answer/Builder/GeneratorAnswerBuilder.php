<?php

/*
 * This file is part of the Phony package.
 *
 * Copyright © 2016 Erin Millard
 *
 * For the full copyright and license information, please view the LICENSE file
 * that was distributed with this source code.
 */

namespace Eloquent\Phony\Stub\Answer\Builder;

use Eloquent\Phony\Call\Arguments;
use Eloquent\Phony\Invocation\InvocableInspector;
use Eloquent\Phony\Invocation\Invoker;
use Eloquent\Phony\Mock\Handle\InstanceHandle;
use Eloquent\Phony\Stub\Answer\Builder\Detail\GeneratorAnswerBuilderDetail;
use Eloquent\Phony\Stub\Answer\Builder\Detail\GeneratorAnswerBuilderDetailWithReturn;
use Eloquent\Phony\Stub\Answer\CallRequest;
use Eloquent\Phony\Stub\Stub;
use Exception;
use RuntimeException;

/**
 * Builds generator stub answers.
 */
class GeneratorAnswerBuilder
{
    /**
     * Construct a new generator answer builder.
     *
     * @param Stub               $stub                       The stub.
     * @param bool               $isGeneratorReturnSupported True if generator return values are supported.
     * @param InvocableInspector $invocableInspector         The invocable inspector to use.
     * @param Invoker            $invoker                    The invoker to use.
     */
    public function __construct(
        Stub $stub,
        $isGeneratorReturnSupported,
        InvocableInspector $invocableInspector,
        Invoker $invoker
    ) {
        $this->stub = $stub;
        $this->isGeneratorReturnSupported = $isGeneratorReturnSupported;
        $this->invocableInspector = $invocableInspector;
        $this->invoker = $invoker;

        $this->requests = array();
        $this->iterations = array();
        $this->returnsSelf = false;
    }

    /**
     * Add a callback to be called as part of the answer.
     *
     * @param callable $callback               The callback.
     * @param callable ...$additionalCallbacks Additional callbacks.
     *
     * @return $this This builder.
     */
    public function calls($callback)
    {
        foreach (func_get_args() as $callback) {
            $this->callsWith($callback);
        }

        return $this;
    }

    /**
     * Add a callback to be called as part of the answer.
     *
     * This method supports reference parameters.
     *
     * @param callable        $callback              The callback.
     * @param Arguments|array $arguments             The arguments.
     * @param bool|null       $prefixSelf            True if the self value should be prefixed.
     * @param bool            $suffixArgumentsObject True if the arguments object should be appended.
     * @param bool            $suffixArguments       True if the arguments should be appended individually.
     */
    public function callsWith(
        $callback,
        $arguments = array(),
        $prefixSelf = null,
        $suffixArgumentsObject = false,
        $suffixArguments = true
    ) {
        if (null === $prefixSelf) {
            $parameters = $this->invocableInspector
                ->callbackReflector($callback)->getParameters();

            $prefixSelf = $parameters &&
                'phonySelf' === $parameters[0]->getName();
        }

        if (!$arguments instanceof Arguments) {
            $arguments = new Arguments($arguments);
        }

        $this->requests[] = new CallRequest(
            $callback,
            $arguments,
            $prefixSelf,
            $suffixArgumentsObject,
            $suffixArguments
        );

        return $this;
    }

    /**
     * Add an argument callback to be called as part of the answer.
     *
     * Negative indices are offset from the end of the list. That is, `-1`
     * indicates the last element, and `-2` indicates the second last element.
     *
     * @param int $index                The argument index.
     * @param int ...$additionalIndices Additional argument indices to call.
     *
     * @return $this This builder.
     */
    public function callsArgument($index = 0)
    {
        if ($arguments = func_get_args()) {
            foreach ($arguments as $index) {
                $this->callsArgumentWith($index);
            }
        } else {
            $this->callsArgumentWith(0);
        }

        return $this;
    }

    /**
     * Add an argument callback to be called as part of the answer.
     *
     * Negative indices are offset from the end of the list. That is, `-1`
     * indicates the last element, and `-2` indicates the second last element.
     *
     * @param int             $index                 The argument index.
     * @param Arguments|array $arguments             The arguments.
     * @param bool            $prefixSelf            True if the self value should be prefixed.
     * @param bool            $suffixArgumentsObject True if the arguments object should be appended.
     * @param bool            $suffixArguments       True if the arguments should be appended individually.
     *
     * @return $this This builder.
     */
    public function callsArgumentWith(
        $index = 0,
        $arguments = array(),
        $prefixSelf = false,
        $suffixArgumentsObject = false,
        $suffixArguments = true
    ) {
        $invoker = $this->invoker;

        if (!$arguments instanceof Arguments) {
            $arguments = new Arguments($arguments);
        }

        return $this->callsWith(
            function ($self, $incoming) use (
                $invoker,
                $index,
                $arguments,
                $prefixSelf,
                $suffixArgumentsObject,
                $suffixArguments
            ) {
                $callback = $incoming->get($index);

                $request = new CallRequest(
                    $callback,
                    $arguments,
                    $prefixSelf,
                    $suffixArgumentsObject,
                    $suffixArguments
                );
                $finalArguments = $request->finalArguments($self, $incoming);

                return $invoker->callWith($callback, $finalArguments);
            },
            array(),
            true,
            true,
            false
        );
    }

    /**
     * Set the value of an argument passed by reference as part of the answer.
     *
     * If called with no arguments, sets the first argument to null.
     *
     * If called with one argument, sets the first argument to $indexOrValue.
     *
     * If called with two arguments, sets the argument at $indexOrValue to
     * $value.
     *
     * @param mixed $indexOrValue The index, or value if no index is specified.
     * @param mixed $value        The value.
     *
     * @return $this This builder.
     */
    public function setsArgument($indexOrValue = null, $value = null)
    {
        if (func_num_args() > 1) {
            $index = $indexOrValue;
        } else {
            $index = 0;
            $value = $indexOrValue;
        }

        if ($value instanceof InstanceHandle) {
            $value = $value->get();
        }

        return $this->callsWith(
            function ($arguments) use ($index, $value) {
                $arguments->set($index, $value);
            },
            array(),
            false,
            true,
            false
        );
    }

    /**
     * Add a yielded value to the answer.
     *
     * If both `$keyOrValue` and `$value` are supplied, the stub will yield like
     * `yield $keyOrValue => $value;`.
     *
     * If only `$keyOrValue` is supplied, the stub will yield like
     * `yield $keyOrValue;`.
     *
     * If no arguments are supplied, the stub will yield like `yield;`.
     *
     * @param mixed $keyOrValue The key or value.
     * @param mixed $value      The value.
     *
     * @return $this This builder.
     */
    public function yields($keyOrValue = null, $value = null)
    {
        $argumentCount = func_num_args();

        if ($argumentCount > 1) {
            $hasKey = true;
            $hasValue = true;
            $key = $keyOrValue;
        } elseif ($argumentCount > 0) {
            $hasKey = false;
            $hasValue = true;
            $key = null;
            $value = $keyOrValue;
        } else {
            $hasKey = false;
            $hasValue = false;
            $key = null;
        }

        if ($key instanceof InstanceHandle) {
            $key = $key->get();
        }

        if ($value instanceof InstanceHandle) {
            $value = $value->get();
        }

        $this->iterations[] = new GeneratorYieldIteration(
            $this->requests,
            $hasKey,
            $key,
            $hasValue,
            $value
        );
        $this->requests = array();

        return $this;
    }

    /**
     * Add a set of yielded values to the answer.
     *
     * @param mixed<mixed,mixed> $values The set of keys and values to yield.
     *
     * @return $this This builder.
     */
    public function yieldsFrom($values)
    {
        $this->iterations[] =
            new GeneratorYieldFromIteration($this->requests, $values);
        $this->requests = array();

        return $this;
    }

    /**
     * End the generator by returning a value.
     *
     * @param mixed $value               The return value.
     * @param mixed ...$additionalValues Additional return values for subsequent invocations.
     *
     * @return Stub             The stub.
     * @throws RuntimeException If the current runtime does not support the supplied return value.
     */
    public function returns($value = null)
    {
        $argumentCount = func_num_args();
        $copies = array();

        for ($i = 1; $i < $argumentCount; ++$i) {
            $copies[$i] = clone $this;
        }

        if ($value instanceof InstanceHandle) {
            $value = $value->get();
        }

        if ($this->isGeneratorReturnSupported || null === $value) {
            $this->returnValue = $value;
            $this->returnsArgument = null;
            $this->returnsSelf = false;
            // @codeCoverageIgnoreStart
        } else {
            throw new RuntimeException(
                'The current runtime does not support the supplied generator ' .
                'return value.'
            );
        }
        // @codeCoverageIgnoreEnd

        for ($i = 1; $i < $argumentCount; ++$i) {
            $this->stub
                ->doesWith($copies[$i]->answer(), array(), true, true, false);

            $copies[$i]->returns(func_get_arg($i));
        }

        return $this->stub;
    }

    /**
     * End the generator by returning an argument.
     *
     * Negative indices are offset from the end of the list. That is, `-1`
     * indicates the last element, and `-2` indicates the second last element.
     *
     * @param int $index The argument index.
     *
     * @return Stub The stub.
     */
    public function returnsArgument($index = 0)
    {
        // @codeCoverageIgnoreStart
        if (!$this->isGeneratorReturnSupported) {
            throw new RuntimeException(
                'The current runtime does not support generator return values.'
            );
        }
        // @codeCoverageIgnoreEnd

        $this->returnsArgument = $index;

        return $this->stub;
    }

    /**
     * End the generator by returning the self value.
     *
     * @return Stub The stub.
     */
    public function returnsSelf()
    {
        // @codeCoverageIgnoreStart
        if (!$this->isGeneratorReturnSupported) {
            throw new RuntimeException(
                'The current runtime does not support generator return values.'
            );
        }
        // @codeCoverageIgnoreEnd

        $this->returnsSelf = true;

        return $this->stub;
    }

    /**
     * End the generator by throwing an exception.
     *
     * @param Exception|Error|string|null $exception               The exception, or message, or null to throw a generic exception.
     * @param Exception|Error|string      ...$additionalExceptions Additional exceptions, or messages, for subsequent invocations.
     *
     * @return Stub The stub.
     */
    public function throws($exception = null)
    {
        $argumentCount = func_num_args();
        $copies = array();

        for ($i = 1; $i < $argumentCount; ++$i) {
            $copies[$i] = clone $this;
        }

        if (is_string($exception)) {
            $exception = new Exception($exception);
        } elseif ($exception instanceof InstanceHandle) {
            $exception = $exception->get();
        } elseif (!$exception) {
            $exception = new Exception();
        }

        $this->exception = $exception;

        for ($i = 1; $i < $argumentCount; ++$i) {
            $this->stub
                ->doesWith($copies[$i]->answer(), array(), true, true, false);

            $copies[$i]->throws(func_get_arg($i));
        }

        return $this->stub;
    }

    /**
     * Get the answer.
     *
     * @return callable The answer.
     */
    public function answer()
    {
        if ($this->isGeneratorReturnSupported) {
            return GeneratorAnswerBuilderDetailWithReturn::answer(
                $this->iterations,
                $this->requests,
                $this->exception,
                $this->returnValue,
                $this->returnsArgument,
                $this->returnsSelf,
                $this->invoker
            );
        }

        // @codeCoverageIgnoreStart
        return GeneratorAnswerBuilderDetail::answer(
            $this->iterations,
            $this->requests,
            $this->exception,
            $this->returnValue,
            $this->returnsArgument,
            $this->returnsSelf,
            $this->invoker
        );
        // @codeCoverageIgnoreEnd
    }

    /**
     * Clone this builder.
     */
    public function __clone()
    {
        // explicitly break references
        foreach (get_object_vars($this) as $property => $value) {
            unset($this->$property);
            $this->$property = $value;
        }
    }

    private $stub;
    private $isGeneratorReturnSupported;
    private $invocableInspector;
    private $invoker;
    private $requests;
    private $iterations;
    private $exception;
    private $returnValue;
    private $returnsArgument;
    private $returnsSelf;
}

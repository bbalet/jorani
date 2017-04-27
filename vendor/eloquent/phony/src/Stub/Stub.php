<?php

/*
 * This file is part of the Phony package.
 *
 * Copyright © 2016 Erin Millard
 *
 * For the full copyright and license information, please view the LICENSE file
 * that was distributed with this source code.
 */

namespace Eloquent\Phony\Stub;

use Eloquent\Phony\Call\Arguments;
use Eloquent\Phony\Invocation\WrappedInvocable;
use Eloquent\Phony\Stub\Answer\Builder\GeneratorAnswerBuilder;
use Error;
use Exception;

/**
 * The interface implemented by stubs.
 */
interface Stub extends WrappedInvocable
{
    /**
     * Set the self value of this stub.
     *
     * This value is used by returnsSelf().
     *
     * @param mixed $self The self value.
     *
     * @return $this This stub.
     */
    public function setSelf($self);

    /**
     * Get the self value of this stub.
     *
     * @return mixed The self value.
     */
    public function self();

    /**
     * Set the callback to use when creating a default answer.
     *
     * @param callable $defaultAnswerCallback The default answer callback.
     *
     * @return $this This stub.
     */
    public function setDefaultAnswerCallback($defaultAnswerCallback);

    /**
     * Get the default answer callback.
     *
     * @return callable The default answer callback.
     */
    public function defaultAnswerCallback();

    /**
     * Modify the current criteria to match the supplied arguments.
     *
     * @param mixed ...$argument The arguments.
     *
     * @return $this This stub.
     */
    public function with();

    /**
     * Add a callback to be called as part of an answer.
     *
     * Note that all supplied callbacks will be called in the same invocation.
     *
     * @param callable $callback               The callback.
     * @param callable ...$additionalCallbacks Additional callbacks.
     *
     * @return $this This stub.
     */
    public function calls($callback);

    /**
     * Add a callback to be called as part of an answer.
     *
     * This method supports reference parameters.
     *
     * Note that all supplied callbacks will be called in the same invocation.
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
    );

    /**
     * Add an argument callback to be called as part of an answer.
     *
     * Negative indices are offset from the end of the list. That is, `-1`
     * indicates the last element, and `-2` indicates the second last element.
     *
     * Note that all supplied callbacks will be called in the same invocation.
     *
     * @param int $index                The argument index.
     * @param int ...$additionalIndices Additional argument indices to call.
     *
     * @return $this This stub.
     */
    public function callsArgument($index = 0);

    /**
     * Add an argument callback to be called as part of an answer.
     *
     * Negative indices are offset from the end of the list. That is, `-1`
     * indicates the last element, and `-2` indicates the second last element.
     *
     * Note that all supplied callbacks will be called in the same invocation.
     *
     * @param int             $index                 The argument index.
     * @param Arguments|array $arguments             The arguments.
     * @param bool            $prefixSelf            True if the self value should be prefixed.
     * @param bool            $suffixArgumentsObject True if the arguments object should be appended.
     * @param bool            $suffixArguments       True if the arguments should be appended individually.
     *
     * @return $this This stub.
     */
    public function callsArgumentWith(
        $index = 0,
        $arguments = array(),
        $prefixSelf = false,
        $suffixArgumentsObject = false,
        $suffixArguments = true
    );

    /**
     * Set the value of an argument passed by reference as part of an answer.
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
     * @return $this This stub.
     */
    public function setsArgument($indexOrValue = null, $value = null);

    /**
     * Add a callback as an answer.
     *
     * @param callable $callback               The callback.
     * @param callable ...$additionalCallbacks Additional callbacks for subsequent invocations.
     *
     * @return $this This stub.
     */
    public function does($callback);

    /**
     * Add a callback as an answer.
     *
     * @param callable        $callback              The callback.
     * @param Arguments|array $arguments             The arguments.
     * @param bool|null       $prefixSelf            True if the self value should be prefixed.
     * @param bool            $suffixArgumentsObject True if the arguments object should be appended.
     * @param bool            $suffixArguments       True if the arguments should be appended individually.
     *
     * @return $this This stub.
     */
    public function doesWith(
        $callback,
        $arguments = array(),
        $prefixSelf = null,
        $suffixArgumentsObject = false,
        $suffixArguments = true
    );

    /**
     * Add an answer that calls the wrapped callback.
     *
     * @param Arguments|array $arguments             The arguments.
     * @param bool|null       $prefixSelf            True if the self value should be prefixed.
     * @param bool            $suffixArgumentsObject True if the arguments object should be appended.
     * @param bool            $suffixArguments       True if the arguments should be appended individually.
     *
     * @return $this This stub.
     */
    public function forwards(
        $arguments = array(),
        $prefixSelf = null,
        $suffixArgumentsObject = false,
        $suffixArguments = true
    );

    /**
     * Add an answer that returns a value.
     *
     * @param mixed $value               The return value.
     * @param mixed ...$additionalValues Additional return values for subsequent invocations.
     *
     * @return $this This stub.
     */
    public function returns($value = null);

    /**
     * Add an answer that returns an argument.
     *
     * Negative indices are offset from the end of the list. That is, `-1`
     * indicates the last element, and `-2` indicates the second last element.
     *
     * @param int $index The argument index.
     *
     * @return $this This stub.
     */
    public function returnsArgument($index = 0);

    /**
     * Add an answer that returns the self value.
     *
     * @return $this This stub.
     */
    public function returnsSelf();

    /**
     * Add an answer that throws an exception.
     *
     * @param Exception|Error|string|null $exception               The exception, or message, or null to throw a generic exception.
     * @param Exception|Error|string      ...$additionalExceptions Additional exceptions, or messages, for subsequent invocations.
     *
     * @return $this This stub.
     */
    public function throws($exception = null);

    /**
     * Add an answer that returns a generator, and return a builder for
     * customizing the generator's behavior.
     *
     * @param mixed<mixed,mixed> $values              A set of keys and values to yield.
     * @param mixed<mixed,mixed> ...$additionalValues Additional sets of keys and values to yield, for subsequent invocations.
     *
     * @return GeneratorAnswerBuilder The answer builder.
     */
    public function generates($values = array());

    /**
     * Close any existing rule.
     *
     * @return $this This stub.
     */
    public function closeRule();
}

<?php

/*
 * This file is part of the Phony package.
 *
 * Copyright © 2016 Erin Millard
 *
 * For the full copyright and license information, please view the LICENSE file
 * that was distributed with this source code.
 */

namespace Eloquent\Phony\Stub\Answer;

use Eloquent\Phony\Call\Arguments;
use Eloquent\Phony\Mock\Handle\InstanceHandle;

/**
 * Represents a call request.
 */
class CallRequest
{
    /**
     * Construct a call request.
     *
     * @param callable  $callback              The callback.
     * @param Arguments $arguments             The arguments.
     * @param bool      $prefixSelf            True if the self value should be prefixed.
     * @param bool      $suffixArgumentsObject True if the arguments object should be appended.
     * @param bool      $suffixArguments       True if the arguments should be appended individually.
     */
    public function __construct(
        $callback,
        Arguments $arguments,
        $prefixSelf,
        $suffixArgumentsObject,
        $suffixArguments
    ) {
        $this->callback = $callback;
        $this->arguments = $arguments;
        $this->prefixSelf = $prefixSelf;
        $this->suffixArgumentsObject = $suffixArgumentsObject;
        $this->suffixArguments = $suffixArguments;

        foreach ($this->arguments->all() as $index => $argument) {
            if ($argument instanceof InstanceHandle) {
                $this->arguments->set($index, $argument->get());
            }
        }
    }

    /**
     * Get the callback.
     *
     * @return callable The callback.
     */
    public function callback()
    {
        return $this->callback;
    }

    /**
     * Get the final arguments.
     *
     * @param object    $self      The self value.
     * @param Arguments $arguments The incoming arguments.
     *
     * @return Arguments The final arguments.
     */
    public function finalArguments($self, Arguments $arguments)
    {
        $finalArguments = $this->arguments->all();

        if ($this->prefixSelf) {
            array_unshift($finalArguments, $self);
        }
        if ($this->suffixArgumentsObject) {
            $finalArguments[] = $arguments;
        }

        if ($this->suffixArguments && $arguments) {
            $finalArguments = array_merge($finalArguments, $arguments->all());
        }

        return new Arguments($finalArguments);
    }

    /**
     * Get the hard-coded arguments.
     *
     * @return Arguments The hard-coded arguments.
     */
    public function arguments()
    {
        return $this->arguments;
    }

    /**
     * Returns true if the self value should be prefixed to the final arguments.
     *
     * @return bool True if the self value should be prefixed.
     */
    public function prefixSelf()
    {
        return $this->prefixSelf;
    }

    /**
     * Returns true if the incoming arguments should be appended to the final
     * arguments as an object.
     *
     * @return bool True if arguments object should be appended.
     */
    public function suffixArgumentsObject()
    {
        return $this->suffixArgumentsObject;
    }

    /**
     * Returns true if the incoming arguments should be appended to the final
     * arguments.
     *
     * @return bool True if arguments should be appended.
     */
    public function suffixArguments()
    {
        return $this->suffixArguments;
    }

    private $callback;
    private $arguments;
    private $prefixSelf;
    private $suffixArgumentsObject;
    private $suffixArguments;
}

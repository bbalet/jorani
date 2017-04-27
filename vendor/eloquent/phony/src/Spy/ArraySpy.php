<?php

/*
 * This file is part of the Phony package.
 *
 * Copyright © 2016 Erin Millard
 *
 * For the full copyright and license information, please view the LICENSE file
 * that was distributed with this source code.
 */

namespace Eloquent\Phony\Spy;

use Eloquent\Phony\Call\Call;
use Eloquent\Phony\Call\Event\CallEventFactory;

/**
 * Spies on an array.
 */
class ArraySpy implements IterableSpy
{
    /**
     * Construct a new array spy.
     *
     * @param Call             $call             The call from which the array originated.
     * @param array            $array            The array.
     * @param CallEventFactory $callEventFactory The call event factory to use.
     */
    public function __construct(
        Call $call,
        array $array,
        CallEventFactory $callEventFactory
    ) {
        $this->call = $call;
        $this->array = $array;
        $this->callEventFactory = $callEventFactory;
        $this->isUsed = false;
        $this->isConsumed = false;
    }

    /**
     * Get the original iterable value.
     *
     * @return mixed The original value.
     */
    public function iterable()
    {
        return $this->array;
    }

    /**
     * Get the current key.
     *
     * @return mixed The current key.
     */
    public function key()
    {
        return key($this->array);
    }

    /**
     * Get the current value.
     *
     * @return mixed The current value.
     */
    public function current()
    {
        return current($this->array);
    }

    /**
     * Move the current position to the next element.
     */
    public function next()
    {
        next($this->array);
    }

    /**
     * Rewind the iterator.
     */
    public function rewind()
    {
        reset($this->array);
    }

    /**
     * Returns true if the current iterator position is valid.
     *
     * @return bool True if the current iterator position is valid.
     */
    public function valid()
    {
        if (!$this->isUsed) {
            $this->call
                ->addIterableEvent($this->callEventFactory->createUsed());
            $this->isUsed = true;
        }

        $key = key($this->array);
        $isValid = null !== $key;

        if ($this->isConsumed) {
            return $isValid;
        }

        if ($isValid) {
            $this->call->addIterableEvent(
                $this->callEventFactory
                    ->createProduced($key, current($this->array))
            );
        } else {
            $this->call->setEndEvent($this->callEventFactory->createConsumed());
            $this->isConsumed = true;
        }

        return $isValid;
    }

    /**
     * Check if a key exists.
     *
     * @param mixed $key The key.
     *
     * @return bool True if the key exists.
     */
    public function offsetExists($key)
    {
        return isset($this->array[$key]);
    }

    /**
     * Get a value.
     *
     * @param mixed $key The key.
     *
     * @return mixed The value.
     */
    public function offsetGet($key)
    {
        return $this->array[$key];
    }

    /**
     * Set a value.
     *
     * @param mixed $key   The key.
     * @param mixed $value The value.
     */
    public function offsetSet($key, $value)
    {
        $this->array[$key] = $value;
    }

    /**
     * Un-set a value.
     *
     * @param mixed $key The key.
     */
    public function offsetUnset($key)
    {
        unset($this->array[$key]);
    }

    /**
     * Get the count.
     *
     * @return int The count.
     */
    public function count()
    {
        return count($this->array);
    }

    private $call;
    private $array;
    private $callEventFactory;
    private $isUsed;
    private $isConsumed;
}

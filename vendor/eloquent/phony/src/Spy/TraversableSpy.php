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
use IteratorAggregate;
use Traversable;

/**
 * Spies on an traversable.
 */
class TraversableSpy implements IterableSpy
{
    /**
     * Construct a new traversable spy.
     *
     * @param Call             $call             The call from which the array originated.
     * @param Traversable      $traversable      The traversable.
     * @param CallEventFactory $callEventFactory The call event factory to use.
     */
    public function __construct(
        Call $call,
        Traversable $traversable,
        CallEventFactory $callEventFactory
    ) {
        $this->call = $call;
        $this->traversable = $traversable;
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
        return $this->traversable;
    }

    /**
     * Get the current key.
     *
     * @return mixed The current key.
     */
    public function key()
    {
        return $this->key;
    }

    /**
     * Get the current value.
     *
     * @return mixed The current value.
     */
    public function current()
    {
        return $this->value;
    }

    /**
     * Move the current position to the next element.
     */
    public function next()
    {
        $this->iterator->next();
    }

    /**
     * Rewind the iterator.
     */
    public function rewind()
    {
        if ($this->iterator) {
            $this->iterator->rewind();
        }
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

            if ($this->traversable instanceof IteratorAggregate) {
                $this->iterator = $this->traversable->getIterator();
            } else {
                $this->iterator = $this->traversable;
            }

            $this->isUsed = true;
        }

        if ($isValid = $this->iterator->valid()) {
            $this->key = $this->iterator->key();
            $this->value = $this->iterator->current();
        } else {
            $this->key = null;
            $this->value = null;
        }

        if ($this->isConsumed) {
            return $isValid;
        }

        if ($isValid) {
            $this->call->addIterableEvent(
                $this->callEventFactory
                    ->createProduced($this->key, $this->value)
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
        return isset($this->traversable[$key]);
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
        return $this->traversable[$key];
    }

    /**
     * Set a value.
     *
     * @param mixed $key   The key.
     * @param mixed $value The value.
     */
    public function offsetSet($key, $value)
    {
        $this->traversable[$key] = $value;
    }

    /**
     * Un-set a value.
     *
     * @param mixed $key The key.
     */
    public function offsetUnset($key)
    {
        unset($this->traversable[$key]);
    }

    /**
     * Get the count.
     *
     * @return int The count.
     */
    public function count()
    {
        return count($this->traversable);
    }

    private $call;
    private $traversable;
    private $callEventFactory;
    private $isUsed;
    private $isConsumed;
    private $iterator;
    private $key;
    private $value;
}

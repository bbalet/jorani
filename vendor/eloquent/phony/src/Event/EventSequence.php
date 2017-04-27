<?php

/*
 * This file is part of the Phony package.
 *
 * Copyright © 2016 Erin Millard
 *
 * For the full copyright and license information, please view the LICENSE file
 * that was distributed with this source code.
 */

namespace Eloquent\Phony\Event;

use ArrayIterator;
use Eloquent\Phony\Call\Call;
use Eloquent\Phony\Call\CallVerifierFactory;
use Eloquent\Phony\Call\Exception\UndefinedCallException;
use Eloquent\Phony\Event\Exception\UndefinedEventException;
use Iterator;

/**
 * Represents a sequence of events.
 */
class EventSequence implements EventCollection
{
    /**
     * Construct a new event sequence.
     *
     * @param array<Event>        $events              The events.
     * @param CallVerifierFactory $callVerifierFactory The call verifier factory to use.
     */
    public function __construct(
        array $events,
        CallVerifierFactory $callVerifierFactory
    ) {
        $calls = array();

        foreach ($events as $event) {
            if ($event instanceof Call) {
                $calls[] = $event;
            }
        }

        $this->events = $events;
        $this->calls = $calls;
        $this->eventCount = count($events);
        $this->callCount = count($calls);
        $this->callVerifierFactory = $callVerifierFactory;
    }

    /**
     * Returns true if this collection contains any events.
     *
     * @return bool True if this collection contains any events.
     */
    public function hasEvents()
    {
        return $this->eventCount > 0;
    }

    /**
     * Returns true if this collection contains any calls.
     *
     * @return bool True if this collection contains any calls.
     */
    public function hasCalls()
    {
        return $this->callCount > 0;
    }

    /**
     * Get the number of events.
     *
     * @return int The event count.
     */
    public function eventCount()
    {
        return $this->eventCount;
    }

    /**
     * Get the number of calls.
     *
     * @return int The call count.
     */
    public function callCount()
    {
        return $this->callCount;
    }

    /**
     * Get the event count.
     *
     * @return int The event count.
     */
    public function count()
    {
        return $this->eventCount;
    }

    /**
     * Get all events as an array.
     *
     * @return array<Event> The events.
     */
    public function allEvents()
    {
        return $this->events;
    }

    /**
     * Get all calls as an array.
     *
     * @return array<Call> The calls.
     */
    public function allCalls()
    {
        return $this->callVerifierFactory->fromCalls($this->calls);
    }

    /**
     * Get the first event.
     *
     * @return Event                   The event.
     * @throws UndefinedEventException If there are no events.
     */
    public function firstEvent()
    {
        if (empty($this->events)) {
            throw new UndefinedEventException(0);
        }

        return $this->events[0];
    }

    /**
     * Get the last event.
     *
     * @return Event                   The event.
     * @throws UndefinedEventException If there are no events.
     */
    public function lastEvent()
    {
        if ($count = count($this->events)) {
            return $this->events[$count - 1];
        }

        throw new UndefinedEventException(0);
    }

    /**
     * Get an event by index.
     *
     * Negative indices are offset from the end of the list. That is, `-1`
     * indicates the last element, and `-2` indicates the second last element.
     *
     * @param int $index The index.
     *
     * @return Event                   The event.
     * @throws UndefinedEventException If the requested event is undefined, or there are no events.
     */
    public function eventAt($index = 0)
    {
        if (!$this->normalizeIndex($this->eventCount, $index, $normalized)) {
            throw new UndefinedEventException($index);
        }

        return $this->events[$normalized];
    }

    /**
     * Get the first call.
     *
     * @return Call                   The call.
     * @throws UndefinedCallException If there are no calls.
     */
    public function firstCall()
    {
        if (isset($this->calls[0])) {
            return $this->callVerifierFactory->fromCall($this->calls[0]);
        }

        throw new UndefinedCallException(0);
    }

    /**
     * Get the last call.
     *
     * @return Call                   The call.
     * @throws UndefinedCallException If there are no calls.
     */
    public function lastCall()
    {
        if ($this->callCount) {
            return $this->callVerifierFactory
                ->fromCall($this->calls[$this->callCount - 1]);
        }

        throw new UndefinedCallException(0);
    }

    /**
     * Get a call by index.
     *
     * Negative indices are offset from the end of the list. That is, `-1`
     * indicates the last element, and `-2` indicates the second last element.
     *
     * @param int $index The index.
     *
     * @return Call                   The call.
     * @throws UndefinedCallException If the requested call is undefined, or there are no calls.
     */
    public function callAt($index = 0)
    {
        if (!$this->normalizeIndex($this->callCount, $index, $normalized)) {
            throw new UndefinedCallException($index);
        }

        return $this->callVerifierFactory->fromCall($this->calls[$normalized]);
    }

    /**
     * Get an iterator for this collection.
     *
     * @return Iterator The iterator.
     */
    public function getIterator()
    {
        return new ArrayIterator($this->events);
    }

    private function normalizeIndex($size, $index, &$normalized = null)
    {
        $normalized = null;

        if ($index < 0) {
            $potential = $size + $index;

            if ($potential < 0) {
                return false;
            }
        } else {
            $potential = $index;
        }

        if ($potential >= $size) {
            return false;
        }

        $normalized = $potential;

        return true;
    }

    private $events;
    private $calls;
    private $eventCount;
    private $callCount;
    private $callVerifierFactory;
}

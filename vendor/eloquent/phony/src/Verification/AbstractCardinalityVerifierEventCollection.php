<?php

/*
 * This file is part of the Phony package.
 *
 * Copyright © 2016 Erin Millard
 *
 * For the full copyright and license information, please view the LICENSE file
 * that was distributed with this source code.
 */

namespace Eloquent\Phony\Verification;

use ArrayIterator;
use Eloquent\Phony\Call\Call;
use Eloquent\Phony\Call\CallVerifierFactory;
use Eloquent\Phony\Call\Exception\UndefinedCallException;
use Eloquent\Phony\Event\Event;
use Eloquent\Phony\Event\EventCollection;
use Eloquent\Phony\Event\Exception\UndefinedEventException;
use Eloquent\Phony\Verification\Exception\InvalidCardinalityException;

/**
 * An abstract base class for implementing cardinality verifiers and an event
 * collection at the same time.
 */
abstract class AbstractCardinalityVerifierEventCollection implements
    CardinalityVerifier,
    EventCollection
{
    /**
     * Construct a new cardinality verifier event collection.
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

        $this->resetCardinality();
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
        if ($this->eventCount < 1) {
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
        if ($this->eventCount) {
            return $this->events[$this->eventCount - 1];
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
        return new ArrayIterator($this->allCalls());
    }

    /**
     * Requires that the next verification never matches.
     *
     * @return $this This verifier.
     */
    public function never()
    {
        return $this->times(0);
    }

    /**
     * Requires that the next verification matches only once.
     *
     * @return $this This verifier.
     */
    public function once()
    {
        return $this->times(1);
    }

    /**
     * Requires that the next verification matches exactly two times.
     *
     * @return $this This verifier.
     */
    public function twice()
    {
        return $this->times(2);
    }

    /**
     * Requires that the next verification matches exactly three times.
     *
     * @return $this This verifier.
     */
    public function thrice()
    {
        return $this->times(3);
    }

    /**
     * Requires that the next verification matches an exact number of times.
     *
     * @param int $times The match count.
     *
     * @return $this This verifier.
     */
    public function times($times)
    {
        return $this->between($times, $times);
    }

    /**
     * Requires that the next verification matches a number of times greater
     * than or equal to $minimum.
     *
     * @param int $minimum The minimum match count.
     *
     * @return $this This verifier.
     */
    public function atLeast($minimum)
    {
        return $this->between($minimum, null);
    }

    /**
     * Requires that the next verification matches a number of times less than
     * or equal to $maximum.
     *
     * @param int $maximum The maximum match count.
     *
     * @return $this This verifier.
     */
    public function atMost($maximum)
    {
        return $this->between(null, $maximum);
    }

    /**
     * Requires that the next verification matches a number of times greater
     * than or equal to $minimum, and less than or equal to $maximum.
     *
     * @param int      $minimum The minimum match count.
     * @param int|null $maximum The maximum match count, or null for no maximum.
     *
     * @return $this                       This verifier.
     * @throws InvalidCardinalityException If the cardinality is invalid.
     */
    public function between($minimum, $maximum)
    {
        $this->cardinality = new Cardinality($minimum, $maximum);

        return $this;
    }

    /**
     * Requires that the next verification matches for all possible items.
     *
     * @return $this This verifier.
     */
    public function always()
    {
        $this->cardinality->setIsAlways(true);

        return $this;
    }

    /**
     * Reset the cardinality to its default value.
     *
     * @return Cardinality The current cardinality.
     */
    public function resetCardinality()
    {
        $cardinality = $this->cardinality;
        $this->atLeast(1);

        return $cardinality;
    }

    /**
     * Get the cardinality.
     *
     * @return Cardinality The cardinality.
     */
    public function cardinality()
    {
        return $this->cardinality;
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

    protected $events;
    protected $calls;
    protected $eventCount;
    protected $callCount;
    protected $callVerifierFactory;
    protected $cardinality;
}

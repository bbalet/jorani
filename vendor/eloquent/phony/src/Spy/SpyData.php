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

use ArrayIterator;
use Eloquent\Phony\Call\Arguments;
use Eloquent\Phony\Call\Call;
use Eloquent\Phony\Call\CallFactory;
use Eloquent\Phony\Call\Event\ThrewEvent;
use Eloquent\Phony\Call\Exception\UndefinedCallException;
use Eloquent\Phony\Event\Event;
use Eloquent\Phony\Event\Exception\UndefinedEventException;
use Eloquent\Phony\Invocation\AbstractWrappedInvocable;
use Eloquent\Phony\Invocation\Invoker;
use Error;
use Exception;
use Generator;
use Iterator;
use Traversable;

/**
 * Spies on a function or method.
 */
class SpyData extends AbstractWrappedInvocable implements Spy
{
    /**
     * Construct a new spy.
     *
     * @param callable|null       $callback            The callback, or null to create an anonymous spy.
     * @param string|null         $label               The label.
     * @param CallFactory         $callFactory         The call factory to use.
     * @param Invoker             $invoker             The invoker to use.
     * @param GeneratorSpyFactory $generatorSpyFactory The generator spy factory to use.
     * @param IterableSpyFactory  $iterableSpyFactory  The iterable spy factory to use.
     */
    public function __construct(
        $callback,
        $label,
        CallFactory $callFactory,
        Invoker $invoker,
        GeneratorSpyFactory $generatorSpyFactory,
        IterableSpyFactory $iterableSpyFactory
    ) {
        parent::__construct($callback, $label);

        $this->callFactory = $callFactory;
        $this->invoker = $invoker;
        $this->generatorSpyFactory = $generatorSpyFactory;
        $this->iterableSpyFactory = $iterableSpyFactory;

        $this->calls = array();
        $this->useGeneratorSpies = true;
        $this->useIterableSpies = false;
        $this->isRecording = true;
    }

    /**
     * Get the next call index.
     *
     * @return int The index.
     */
    public function nextIndex()
    {
        return count($this->calls);
    }

    /**
     * Turn on or off the use of generator spies.
     *
     * @param bool $useGeneratorSpies True to use generator spies.
     *
     * @return $this This spy.
     */
    public function setUseGeneratorSpies($useGeneratorSpies)
    {
        $this->useGeneratorSpies = $useGeneratorSpies;

        return $this;
    }

    /**
     * Returns true if this spy uses generator spies.
     *
     * @return bool True if this spy uses generator spies.
     */
    public function useGeneratorSpies()
    {
        return $this->useGeneratorSpies;
    }

    /**
     * Turn on or off the use of iterable spies.
     *
     * @param bool $useIterableSpies True to use iterable spies.
     *
     * @return $this This spy.
     */
    public function setUseIterableSpies($useIterableSpies)
    {
        $this->useIterableSpies = $useIterableSpies;

        return $this;
    }

    /**
     * Returns true if this spy uses iterable spies.
     *
     * @return bool True if this spy uses iterable spies.
     */
    public function useIterableSpies()
    {
        return $this->useIterableSpies;
    }

    /**
     * Stop recording calls.
     *
     * @return $this This spy.
     */
    public function stopRecording()
    {
        $this->isRecording = false;

        return $this;
    }

    /**
     * Start recording calls.
     *
     * @return $this This spy.
     */
    public function startRecording()
    {
        $this->isRecording = true;

        return $this;
    }

    /**
     * Set the calls.
     *
     * @param array<Call> $calls The calls.
     */
    public function setCalls(array $calls)
    {
        $this->calls = $calls;
    }

    /**
     * Add a call.
     *
     * @param Call $call The call.
     */
    public function addCall(Call $call)
    {
        $this->calls[] = $call;
    }

    /**
     * Returns true if this collection contains any events.
     *
     * @return bool True if this collection contains any events.
     */
    public function hasEvents()
    {
        return (bool) $this->calls;
    }

    /**
     * Returns true if this collection contains any calls.
     *
     * @return bool True if this collection contains any calls.
     */
    public function hasCalls()
    {
        return (bool) $this->calls;
    }

    /**
     * Get the number of events.
     *
     * @return int The event count.
     */
    public function eventCount()
    {
        return count($this->calls);
    }

    /**
     * Get the number of calls.
     *
     * @return int The call count.
     */
    public function callCount()
    {
        return count($this->calls);
    }

    /**
     * Get the event count.
     *
     * @return int The event count.
     */
    public function count()
    {
        return count($this->calls);
    }

    /**
     * Get all events as an array.
     *
     * @return array<Event> The events.
     */
    public function allEvents()
    {
        return $this->calls;
    }

    /**
     * Get all calls as an array.
     *
     * @return array<Call> The calls.
     */
    public function allCalls()
    {
        return $this->calls;
    }

    /**
     * Get the first event.
     *
     * @return Event                   The event.
     * @throws UndefinedEventException If there are no events.
     */
    public function firstEvent()
    {
        if (empty($this->calls)) {
            throw new UndefinedEventException(0);
        }

        return $this->calls[0];
    }

    /**
     * Get the last event.
     *
     * @return Event                   The event.
     * @throws UndefinedEventException If there are no events.
     */
    public function lastEvent()
    {
        if ($count = count($this->calls)) {
            return $this->calls[$count - 1];
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
        if (!$this->normalizeIndex(count($this->calls), $index, $normalized)) {
            throw new UndefinedEventException($index);
        }

        return $this->calls[$normalized];
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
            return $this->calls[0];
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
        if ($count = count($this->calls)) {
            return $this->calls[$count - 1];
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
        if (!$this->normalizeIndex(count($this->calls), $index, $normalized)) {
            throw new UndefinedCallException($index);
        }

        return $this->calls[$normalized];
    }

    /**
     * Get an iterator for this collection.
     *
     * @return Iterator The iterator.
     */
    public function getIterator()
    {
        return new ArrayIterator($this->calls);
    }

    /**
     * Invoke this object.
     *
     * This method supports reference parameters.
     *
     * @param Arguments|array $arguments The arguments.
     *
     * @return mixed           The result of invocation.
     * @throws Exception|Error If an error occurs.
     */
    public function invokeWith($arguments = array())
    {
        if (!$arguments instanceof Arguments) {
            $arguments = new Arguments($arguments);
        }

        if (!$this->isRecording) {
            return $this->invoker->callWith($this->callback, $arguments);
        }

        $call = $this->callFactory->record($this->callback, $arguments, $this);
        $responseEvent = $call->responseEvent();

        if ($responseEvent instanceof ThrewEvent) {
            $call->setEndEvent($responseEvent);

            throw $responseEvent->exception();
        }

        $returnValue = $responseEvent->value();

        if ($this->useGeneratorSpies && $returnValue instanceof Generator) {
            return $this->generatorSpyFactory->create($call, $returnValue);
        }

        if (
            $this->useIterableSpies &&
            (is_array($returnValue) || $returnValue instanceof Traversable)
        ) {
            return $this->iterableSpyFactory->create($call, $returnValue);
        }

        $call->setEndEvent($call->responseEvent());

        return $returnValue;
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

    private $callFactory;
    private $invoker;
    private $generatorSpyFactory;
    private $iterableSpyFactory;
    private $useGeneratorSpies;
    private $useIterableSpies;
    private $isRecording;
    private $calls;
}

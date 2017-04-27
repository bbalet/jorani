<?php

/*
 * This file is part of the Phony package.
 *
 * Copyright © 2016 Erin Millard
 *
 * For the full copyright and license information, please view the LICENSE file
 * that was distributed with this source code.
 */

namespace Eloquent\Phony\Call;

use Eloquent\Phony\Assertion\AssertionRecorder;
use Eloquent\Phony\Assertion\AssertionRenderer;
use Eloquent\Phony\Call\Event\CalledEvent;
use Eloquent\Phony\Call\Event\EndEvent;
use Eloquent\Phony\Call\Event\IterableEvent;
use Eloquent\Phony\Call\Event\ResponseEvent;
use Eloquent\Phony\Call\Exception\UndefinedArgumentException;
use Eloquent\Phony\Call\Exception\UndefinedCallException;
use Eloquent\Phony\Call\Exception\UndefinedResponseException;
use Eloquent\Phony\Event\Event;
use Eloquent\Phony\Event\EventCollection;
use Eloquent\Phony\Event\Exception\UndefinedEventException;
use Eloquent\Phony\Matcher\Matcher;
use Eloquent\Phony\Matcher\MatcherFactory;
use Eloquent\Phony\Matcher\MatcherVerifier;
use Eloquent\Phony\Mock\Handle\InstanceHandle;
use Eloquent\Phony\Verification\AbstractCardinalityVerifier;
use Eloquent\Phony\Verification\GeneratorVerifier;
use Eloquent\Phony\Verification\GeneratorVerifierFactory;
use Eloquent\Phony\Verification\IterableVerifier;
use Eloquent\Phony\Verification\IterableVerifierFactory;
use Error;
use Exception;
use Generator;
use InvalidArgumentException;
use Iterator;
use Throwable;
use Traversable;

/**
 * Provides convenience methods for verifying the details of a call.
 */
class CallVerifier extends AbstractCardinalityVerifier implements Call
{
    /**
     * Construct a new call verifier.
     *
     * @param Call                     $call                     The call.
     * @param MatcherFactory           $matcherFactory           The matcher factory to use.
     * @param MatcherVerifier          $matcherVerifier          The matcher verifier to use.
     * @param GeneratorVerifierFactory $generatorVerifierFactory The generator verifier factory to use.
     * @param IterableVerifierFactory  $iterableVerifierFactory  The iterable verifier factory to use.
     * @param AssertionRecorder        $assertionRecorder        The assertion recorder to use.
     * @param AssertionRenderer        $assertionRenderer        The assertion renderer to use.
     */
    public function __construct(
        Call $call,
        MatcherFactory $matcherFactory,
        MatcherVerifier $matcherVerifier,
        GeneratorVerifierFactory $generatorVerifierFactory,
        IterableVerifierFactory $iterableVerifierFactory,
        AssertionRecorder $assertionRecorder,
        AssertionRenderer $assertionRenderer
    ) {
        parent::__construct();

        $this->call = $call;
        $this->matcherFactory = $matcherFactory;
        $this->matcherVerifier = $matcherVerifier;
        $this->generatorVerifierFactory = $generatorVerifierFactory;
        $this->iterableVerifierFactory = $iterableVerifierFactory;
        $this->assertionRecorder = $assertionRecorder;
        $this->assertionRenderer = $assertionRenderer;

        $this->argumentCount = count($call->arguments());
    }

    /**
     * Get the call index.
     *
     * This number tracks the order of this call with respect to other calls
     * made against the same spy.
     *
     * @return int The index.
     */
    public function index()
    {
        return $this->call->index();
    }

    /**
     * Get the sequence number.
     *
     * The sequence number is a unique number assigned to every event that Phony
     * records. The numbers are assigned sequentially, meaning that sequence
     * numbers can be used to determine event order.
     *
     * @return int The sequence number.
     */
    public function sequenceNumber()
    {
        return $this->call->sequenceNumber();
    }

    /**
     * Get the time at which the event occurred.
     *
     * @return float The time at which the event occurred, in seconds since the Unix epoch.
     */
    public function time()
    {
        return $this->call->time();
    }

    /**
     * Returns true if this collection contains any events.
     *
     * @return bool True if this collection contains any events.
     */
    public function hasEvents()
    {
        return $this->call->hasEvents();
    }

    /**
     * Returns true if this collection contains any calls.
     *
     * @return bool True if this collection contains any calls.
     */
    public function hasCalls()
    {
        return $this->call->hasCalls();
    }

    /**
     * Get the number of events.
     *
     * @return int The event count.
     */
    public function eventCount()
    {
        return $this->call->eventCount();
    }

    /**
     * Get the number of calls.
     *
     * @return int The call count.
     */
    public function callCount()
    {
        return $this->call->callCount();
    }

    /**
     * Get the event count.
     *
     * @return int The event count.
     */
    public function count()
    {
        return $this->call->count();
    }

    /**
     * Get the first event.
     *
     * @return Event                   The event.
     * @throws UndefinedEventException If there are no events.
     */
    public function firstEvent()
    {
        return $this->call->firstEvent();
    }

    /**
     * Get the last event.
     *
     * @return Event                   The event.
     * @throws UndefinedEventException If there are no events.
     */
    public function lastEvent()
    {
        return $this->call->lastEvent();
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
        return $this->call->eventAt($index);
    }

    /**
     * Get the first call.
     *
     * @return Call                   The call.
     * @throws UndefinedCallException If there are no calls.
     */
    public function firstCall()
    {
        return $this;
    }

    /**
     * Get the last call.
     *
     * @return Call                   The call.
     * @throws UndefinedCallException If there are no calls.
     */
    public function lastCall()
    {
        return $this;
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
        if (0 === $index || -1 === $index) {
            return $this;
        }

        throw new UndefinedCallException($index);
    }

    /**
     * Get an iterator for this collection.
     *
     * @return Iterator The iterator.
     */
    public function getIterator()
    {
        return $this->call->getIterator();
    }

    /**
     * Get the 'called' event.
     *
     * @return CalledEvent The 'called' event.
     */
    public function calledEvent()
    {
        return $this->call->calledEvent();
    }

    /**
     * Set the response event.
     *
     * @param ResponseEvent $responseEvent The response event.
     *
     * @throws InvalidArgumentException If the call has already responded.
     */
    public function setResponseEvent(ResponseEvent $responseEvent)
    {
        $this->call->setResponseEvent($responseEvent);
    }

    /**
     * Get the response event.
     *
     * @return ResponseEvent|null The response event, or null if the call has not yet responded.
     */
    public function responseEvent()
    {
        return $this->call->responseEvent();
    }

    /**
     * Add an iterable event.
     *
     * @param IterableEvent $iterableEvent The iterable event.
     *
     * @throws InvalidArgumentException If the call has already completed.
     */
    public function addIterableEvent(IterableEvent $iterableEvent)
    {
        $this->call->addIterableEvent($iterableEvent);
    }

    /**
     * Get the iterable events.
     *
     * @return array<IterableEvent> The iterable events.
     */
    public function iterableEvents()
    {
        return $this->call->iterableEvents();
    }

    /**
     * Set the end event.
     *
     * @param EndEvent $endEvent The end event.
     *
     * @throws InvalidArgumentException If the call has already completed.
     */
    public function setEndEvent(EndEvent $endEvent)
    {
        $this->call->setEndEvent($endEvent);
    }

    /**
     * Get the end event.
     *
     * @return EndEvent|null The end event, or null if the call has not yet completed.
     */
    public function endEvent()
    {
        return $this->call->endEvent();
    }

    /**
     * Get all events as an array.
     *
     * @return array<Event> The events.
     */
    public function allEvents()
    {
        return $this->call->allEvents();
    }

    /**
     * Get all calls as an array.
     *
     * @return array<Call> The calls.
     */
    public function allCalls()
    {
        return $this->call->allCalls();
    }

    /**
     * Returns true if this call has responded.
     *
     * A call that has responded has returned a value, or thrown an exception.
     *
     * @return bool True if this call has responded.
     */
    public function hasResponded()
    {
        return $this->call->hasResponded();
    }

    /**
     * Returns true if this call has responded with an iterable.
     *
     * @return bool True if this call has responded with an iterable.
     */
    public function isIterable()
    {
        return $this->call->isIterable();
    }

    /**
     * Returns true if this call has responded with a generator.
     *
     * @return bool True if this call has responded with a generator.
     */
    public function isGenerator()
    {
        return $this->call->isGenerator();
    }

    /**
     * Returns true if this call has completed.
     *
     * When generator spies are in use, a call that returns a generator will not
     * be considered complete until the generator has been completely consumed
     * via iteration.
     *
     * Similarly, when iterable spies are in use, a call that returns an
     * iterable will not be considered complete until the iterable has been
     * completely consumed via iteration.
     *
     * @return bool True if this call has completed.
     */
    public function hasCompleted()
    {
        return $this->call->hasCompleted();
    }

    /**
     * Get the callback.
     *
     * @return callable The callback.
     */
    public function callback()
    {
        return $this->call->callback();
    }

    /**
     * Get the received arguments.
     *
     * @return Arguments The received arguments.
     */
    public function arguments()
    {
        return $this->call->arguments();
    }

    /**
     * Get an argument by index.
     *
     * Negative indices are offset from the end of the list. That is, `-1`
     * indicates the last element, and `-2` indicates the second last element.
     *
     * @param int $index The index.
     *
     * @return mixed                      The argument.
     * @throws UndefinedArgumentException If the requested argument is undefined, or no arguments were recorded.
     */
    public function argument($index = 0)
    {
        return $this->call->argument($index);
    }

    /**
     * Get the returned value.
     *
     * @return mixed                      The returned value.
     * @throws UndefinedResponseException If this call has not yet returned a value.
     */
    public function returnValue()
    {
        return $this->call->returnValue();
    }

    /**
     * Get the value returned from the generator.
     *
     * @return mixed                      The returned value.
     * @throws UndefinedResponseException If this call has not yet returned a value via generator.
     */
    public function generatorReturnValue()
    {
        return $this->call->generatorReturnValue();
    }

    /**
     * Get the thrown exception.
     *
     * @return Exception|Error            The thrown exception.
     * @throws UndefinedResponseException If this call has not yet thrown an exception.
     */
    public function exception()
    {
        return $this->call->exception();
    }

    /**
     * Get the exception thrown from the generator.
     *
     * @return Exception|Error            The thrown exception.
     * @throws UndefinedResponseException If this call has not yet thrown an exception via generator.
     */
    public function generatorException()
    {
        return $this->call->generatorException();
    }

    /**
     * Get the response.
     *
     * @return tuple<Exception|Error|null,mixed> A 2-tuple of thrown exception or null, and return value.
     * @throws UndefinedResponseException        If this call has not yet responded.
     */
    public function response()
    {
        return $this->call->response();
    }

    /**
     * Get the response from the generator.
     *
     * @return tuple<Exception|Error|null,mixed> A 2-tuple of thrown exception or null, and return value.
     * @throws UndefinedResponseException        If this call has not yet responded via generator.
     */
    public function generatorResponse()
    {
        return $this->call->generatorResponse();
    }

    /**
     * Get the time at which the call responded.
     *
     * A call that has responded has returned a value, or thrown an exception.
     *
     * @return float|null The time at which the call responded, in seconds since the Unix epoch, or null if the call has not yet responded.
     */
    public function responseTime()
    {
        return $this->call->responseTime();
    }

    /**
     * Get the time at which the call completed.
     *
     * When generator spies are in use, a call that returns a generator will not
     * be considered complete until the generator has been completely consumed
     * via iteration.
     *
     * Similarly, when iterable spies are in use, a call that returns an
     * iterable will not be considered complete until the iterable has been
     * completely consumed via iteration.
     *
     * @return float|null The time at which the call completed, in seconds since the Unix epoch, or null if the call has not yet completed.
     */
    public function endTime()
    {
        return $this->call->endTime();
    }

    /**
     * Get the call duration.
     *
     * @return float|null The call duration in seconds, or null if the call has not yet completed.
     */
    public function duration()
    {
        $endTime = $this->call->endTime();

        if (null === $endTime) {
            return;
        }

        return $endTime - $this->call->time();
    }

    /**
     * Get the call response duration.
     *
     * @return float|null The call response duration in seconds, or null if the call has not yet responded.
     */
    public function responseDuration()
    {
        $responseTime = $this->call->responseTime();

        if (null === $responseTime) {
            return;
        }

        return $responseTime - $this->call->time();
    }

    /**
     * Get the number of arguments.
     *
     * @return int The number of arguments.
     */
    public function argumentCount()
    {
        return $this->argumentCount;
    }

    /**
     * Checks if called with the supplied arguments.
     *
     * @param mixed ...$argument The arguments.
     *
     * @return EventCollection|null        The result.
     * @throws InvalidCardinalityException If the cardinality is invalid.
     */
    public function checkCalledWith()
    {
        $cardinality = $this->resetCardinality()->assertSingular();

        $matchers = $this->matcherFactory->adaptAll(func_get_args());

        list($matchCount, $matchingEvents) = $this->matchIf(
            $this->call,
            $this->matcherVerifier
                ->matches($matchers, $this->call->arguments()->all())
        );

        if ($cardinality->matches($matchCount, 1)) {
            return $this->assertionRecorder->createSuccess($matchingEvents);
        }
    }

    /**
     * Throws an exception unless called with the supplied arguments.
     *
     * @param mixed ...$argument The arguments.
     *
     * @return EventCollection             The result.
     * @throws InvalidCardinalityException If the cardinality is invalid.
     * @throws Exception                   If the assertion fails, and the assertion recorder throws exceptions.
     */
    public function calledWith()
    {
        $cardinality = $this->cardinality;
        $matchers = $this->matcherFactory->adaptAll(func_get_args());

        if (
            $result =
                call_user_func_array(array($this, 'checkCalledWith'), $matchers)
        ) {
            return $result;
        }

        return $this->assertionRecorder->createFailure(
            $this->assertionRenderer
                ->renderCalledWith($this->call, $cardinality, $matchers)
        );
    }

    /**
     * Checks if this call responded.
     *
     * @return EventCollection|null        The result.
     * @throws InvalidCardinalityException If the cardinality is invalid.
     */
    public function checkResponded()
    {
        $cardinality = $this->resetCardinality()->assertSingular();
        $responseEvent = $this->call->responseEvent();

        list($matchCount, $matchingEvents) =
            $this->matchIf($responseEvent, $responseEvent);

        if ($cardinality->matches($matchCount, 1)) {
            return $this->assertionRecorder->createSuccess($matchingEvents);
        }
    }

    /**
     * Throws an exception unless this call responded.
     *
     * @return EventCollection             The result.
     * @throws InvalidCardinalityException If the cardinality is invalid.
     * @throws Exception                   If the assertion fails, and the assertion recorder throws exceptions.
     */
    public function responded()
    {
        $cardinality = $this->cardinality;

        if ($result = $this->checkResponded()) {
            return $result;
        }

        return $this->assertionRecorder->createFailure(
            $this->assertionRenderer->renderResponded($this->call, $cardinality)
        );
    }

    /**
     * Checks if this call completed.
     *
     * @return EventCollection|null        The result.
     * @throws InvalidCardinalityException If the cardinality is invalid.
     */
    public function checkCompleted()
    {
        $cardinality = $this->resetCardinality()->assertSingular();
        $endEvent = $this->call->endEvent();

        list($matchCount, $matchingEvents) =
            $this->matchIf($endEvent, $endEvent);

        if ($cardinality->matches($matchCount, 1)) {
            return $this->assertionRecorder->createSuccess($matchingEvents);
        }
    }

    /**
     * Throws an exception unless this call completed.
     *
     * @return EventCollection             The result.
     * @throws InvalidCardinalityException If the cardinality is invalid.
     * @throws Exception                   If the assertion fails, and the assertion recorder throws exceptions.
     */
    public function completed()
    {
        $cardinality = $this->cardinality;

        if ($result = $this->checkCompleted()) {
            return $result;
        }

        return $this->assertionRecorder->createFailure(
            $this->assertionRenderer->renderCompleted($this->call, $cardinality)
        );
    }

    /**
     * Checks if this call returned the supplied value.
     *
     * When called with no arguments, this method simply checks that the call
     * returned any value.
     *
     * @param mixed $value The value.
     *
     * @return EventCollection|null        The result.
     * @throws InvalidCardinalityException If the cardinality is invalid.
     */
    public function checkReturned($value = null)
    {
        $cardinality = $this->resetCardinality()->assertSingular();

        if ($responseEvent = $this->call->responseEvent()) {
            list($exception, $returnValue) = $this->call->response();

            $hasReturned = !$exception;
        } else {
            $returnValue = null;
            $hasReturned = false;
        }

        if (0 === func_num_args()) {
            list($matchCount, $matchingEvents) =
                $this->matchIf($responseEvent, $hasReturned);

            if ($cardinality->matches($matchCount, 1)) {
                return $this->assertionRecorder->createSuccess($matchingEvents);
            }

            return;
        }

        $value = $this->matcherFactory->adapt($value);

        list($matchCount, $matchingEvents) = $this->matchIf(
            $responseEvent,
            $hasReturned && $value->matches($returnValue)
        );

        if ($cardinality->matches($matchCount, 1)) {
            return $this->assertionRecorder->createSuccess($matchingEvents);
        }
    }

    /**
     * Throws an exception unless this call returned the supplied value.
     *
     * When called with no arguments, this method simply checks that the call
     * returned any value.
     *
     * @param mixed $value The value.
     *
     * @return EventCollection             The result.
     * @throws InvalidCardinalityException If the cardinality is invalid.
     * @throws Exception                   If the assertion fails, and the assertion recorder throws exceptions.
     */
    public function returned($value = null)
    {
        $cardinality = $this->cardinality;
        $argumentCount = func_num_args();

        if (0 === $argumentCount) {
            $arguments = array();
        } else {
            $value = $this->matcherFactory->adapt($value);
            $arguments = array($value);
        }

        if (
            $result =
                call_user_func_array(array($this, 'checkReturned'), $arguments)
        ) {
            return $result;
        }

        return $this->assertionRecorder->createFailure(
            $this->assertionRenderer
                ->renderReturned($this->call, $cardinality, $value)
        );
    }

    /**
     * Checks if an exception of the supplied type was thrown.
     *
     * @param Matcher|Exception|Error|string|null $type An exception to match, the type of exception, or null for any exception.
     *
     * @return EventCollection|null        The result.
     * @throws InvalidCardinalityException If the cardinality is invalid.
     * @throws InvalidArgumentException    If the type is invalid.
     */
    public function checkThrew($type = null)
    {
        $cardinality = $this->resetCardinality()->assertSingular();

        if ($responseEvent = $this->call->responseEvent()) {
            list($exception) = $this->call->response();
        } else {
            $exception = null;
        }

        $isTypeSupported = false;

        if (!$type) {
            $isTypeSupported = true;

            list($matchCount, $matchingEvents) =
                $this->matchIf($responseEvent, $exception);

            if ($cardinality->matches($matchCount, 1)) {
                return $this->assertionRecorder->createSuccess($matchingEvents);
            }
        } elseif (is_string($type)) {
            $isTypeSupported = true;

            list($matchCount, $matchingEvents) =
                $this->matchIf($responseEvent, is_a($exception, $type));

            if ($cardinality->matches($matchCount, 1)) {
                return $this->assertionRecorder->createSuccess($matchingEvents);
            }
        } elseif (is_object($type)) {
            if ($type instanceof InstanceHandle) {
                $type = $type->get();
            }

            if ($type instanceof Throwable || $type instanceof Exception) {
                $isTypeSupported = true;
                $type = $this->matcherFactory->equalTo($type, true);
            } elseif ($this->matcherFactory->isMatcher($type)) {
                $isTypeSupported = true;
                $type = $this->matcherFactory->adapt($type);
            }

            if ($isTypeSupported) {
                list($matchCount, $matchingEvents) = $this->matchIf(
                    $responseEvent,
                    $exception && $type->matches($exception)
                );

                if ($cardinality->matches($matchCount, 1)) {
                    return $this->assertionRecorder
                        ->createSuccess($matchingEvents);
                }
            }
        }

        if (!$isTypeSupported) {
            throw new InvalidArgumentException(
                sprintf(
                    'Unable to match exceptions against %s.',
                    $this->assertionRenderer->renderValue($type)
                )
            );
        }
    }

    /**
     * Throws an exception unless this call threw an exception of the supplied
     * type.
     *
     * @param Matcher|Exception|Error|string|null $type An exception to match, the type of exception, or null for any exception.
     *
     * @return EventCollection             The result.
     * @throws InvalidCardinalityException If the cardinality is invalid.
     * @throws InvalidArgumentException    If the type is invalid.
     * @throws Exception                   If the assertion fails, and the assertion recorder throws exceptions.
     */
    public function threw($type = null)
    {
        $cardinality = $this->cardinality;

        if ($type instanceof InstanceHandle) {
            $type = $type->get();
        }

        if ($type instanceof Throwable || $type instanceof Exception) {
            $type = $this->matcherFactory->equalTo($type, true);
        } elseif ($this->matcherFactory->isMatcher($type)) {
            $type = $this->matcherFactory->adapt($type);
        }

        if ($result = $this->checkThrew($type)) {
            return $result;
        }

        return $this->assertionRecorder->createFailure(
            $this->assertionRenderer
                ->renderThrew($this->call, $cardinality, $type)
        );
    }

    /**
     * Checks if this call returned a generator.
     *
     * @return GeneratorVerifier|null      The result.
     * @throws InvalidCardinalityException If the cardinality is invalid.
     */
    public function checkGenerated()
    {
        $cardinality = $this->resetCardinality()->assertSingular();

        if ($this->call->responseEvent()) {
            list(, $returnValue) = $this->call->response();

            $isMatch = $returnValue instanceof Generator;
        } else {
            $isMatch = false;
        }

        list($matchCount, $matchingEvents) =
            $this->matchIf($this->call, $isMatch);

        if ($cardinality->matches($matchCount, 1)) {
            return $this->assertionRecorder->createSuccessFromEventCollection(
                $this->generatorVerifierFactory
                    ->create($this->call, $matchingEvents)
            );
        }
    }

    /**
     * Throws an exception unless this call returned a generator.
     *
     * @return GeneratorVerifier           The result.
     * @throws InvalidCardinalityException If the cardinality is invalid.
     * @throws Exception                   If the assertion fails, and the assertion recorder throws exceptions.
     */
    public function generated()
    {
        $cardinality = $this->cardinality;

        if ($result = $this->checkGenerated()) {
            return $result;
        }

        return $this->assertionRecorder->createFailure(
            $this->assertionRenderer->renderGenerated($this->call, $cardinality)
        );
    }

    /**
     * Checks if this call returned an iterable.
     *
     * @return IterableVerifier|null       The result.
     * @throws InvalidCardinalityException If the cardinality is invalid.
     */
    public function checkIterated()
    {
        $cardinality = $this->resetCardinality()->assertSingular();

        if ($this->call->responseEvent()) {
            list(, $returnValue) = $this->call->response();

            $isMatch =
                $returnValue instanceof Traversable || is_array($returnValue);
        } else {
            $isMatch = false;
        }

        list($matchCount, $matchingEvents) =
            $this->matchIf($this->call, $isMatch);

        if ($cardinality->matches($matchCount, 1)) {
            return $this->assertionRecorder->createSuccessFromEventCollection(
                $this->iterableVerifierFactory
                    ->create($this->call, $matchingEvents)
            );
        }
    }

    /**
     * Throws an exception unless this call returned an iterable.
     *
     * @return IterableVerifier            The result.
     * @throws InvalidCardinalityException If the cardinality is invalid.
     * @throws Exception                   If the assertion fails, and the assertion recorder throws exceptions.
     */
    public function iterated()
    {
        $cardinality = $this->cardinality;

        if ($result = $this->checkIterated()) {
            return $result;
        }

        return $this->assertionRecorder->createFailure(
            $this->assertionRenderer->renderIterated($this->call, $cardinality)
        );
    }

    private function matchIf($event, $checkResult)
    {
        if ($checkResult && $event) {
            $matchCount = 1;
            $matchingEvents = array($event);
        } else {
            $matchCount = 0;
            $matchingEvents = array();
        }

        return array($matchCount, $matchingEvents);
    }

    private $call;
    private $matcherFactory;
    private $matcherVerifier;
    private $generatorVerifierFactory;
    private $iterableVerifierFactory;
    private $assertionRecorder;
    private $assertionRenderer;
    private $argumentCount;
}

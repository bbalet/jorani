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
use Eloquent\Phony\Assertion\AssertionRecorder;
use Eloquent\Phony\Assertion\AssertionRenderer;
use Eloquent\Phony\Call\Arguments;
use Eloquent\Phony\Call\Call;
use Eloquent\Phony\Call\CallVerifier;
use Eloquent\Phony\Call\CallVerifierFactory;
use Eloquent\Phony\Call\Exception\UndefinedCallException;
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
 * Provides convenience methods for verifying interactions with a spy.
 */
class SpyVerifier extends AbstractCardinalityVerifier implements Spy
{
    /**
     * Construct a new spy verifier.
     *
     * @param Spy                      $spy                      The spy.
     * @param MatcherFactory           $matcherFactory           The matcher factory to use.
     * @param MatcherVerifier          $matcherVerifier          The macther verifier to use.
     * @param GeneratorVerifierFactory $generatorVerifierFactory The generator verifier factory to use.
     * @param IterableVerifierFactory  $iterableVerifierFactory  The iterable verifier factory to use.
     * @param CallVerifierFactory      $callVerifierFactory      The call verifier factory to use.
     * @param AssertionRecorder        $assertionRecorder        The assertion recorder to use.
     * @param AssertionRenderer        $assertionRenderer        The assertion renderer to use.
     */
    public function __construct(
        Spy $spy,
        MatcherFactory $matcherFactory,
        MatcherVerifier $matcherVerifier,
        GeneratorVerifierFactory $generatorVerifierFactory,
        IterableVerifierFactory $iterableVerifierFactory,
        CallVerifierFactory $callVerifierFactory,
        AssertionRecorder $assertionRecorder,
        AssertionRenderer $assertionRenderer
    ) {
        parent::__construct();

        $this->spy = $spy;
        $this->matcherFactory = $matcherFactory;
        $this->matcherVerifier = $matcherVerifier;
        $this->generatorVerifierFactory = $generatorVerifierFactory;
        $this->iterableVerifierFactory = $iterableVerifierFactory;
        $this->callVerifierFactory = $callVerifierFactory;
        $this->assertionRecorder = $assertionRecorder;
        $this->assertionRenderer = $assertionRenderer;
    }

    /**
     * Get the spy.
     *
     * @return Spy The spy.
     */
    public function spy()
    {
        return $this->spy;
    }

    /**
     * Returns true if anonymous.
     *
     * @return bool True if anonymous.
     */
    public function isAnonymous()
    {
        return $this->spy->isAnonymous();
    }

    /**
     * Get the callback.
     *
     * @return callable The callback.
     */
    public function callback()
    {
        return $this->spy->callback();
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
        $this->spy->setUseGeneratorSpies($useGeneratorSpies);

        return $this;
    }

    /**
     * Returns true if this spy uses generator spies.
     *
     * @return bool True if this spy uses generator spies.
     */
    public function useGeneratorSpies()
    {
        return $this->spy->useGeneratorSpies();
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
        $this->spy->setUseIterableSpies($useIterableSpies);

        return $this;
    }

    /**
     * Returns true if this spy uses iterable spies.
     *
     * @return bool True if this spy uses iterable spies.
     */
    public function useIterableSpies()
    {
        return $this->spy->useIterableSpies();
    }

    /**
     * Set the label.
     *
     * @param string|null $label The label.
     *
     * @return $this This invocable.
     */
    public function setLabel($label)
    {
        $this->spy->setLabel($label);

        return $this;
    }

    /**
     * Get the label.
     *
     * @return string|null The label.
     */
    public function label()
    {
        return $this->spy->label();
    }

    /**
     * Stop recording calls.
     *
     * @return $this This spy.
     */
    public function stopRecording()
    {
        $this->spy->stopRecording();

        return $this;
    }

    /**
     * Start recording calls.
     *
     * @return $this This spy.
     */
    public function startRecording()
    {
        $this->spy->startRecording();

        return $this;
    }

    /**
     * Set the calls.
     *
     * @param array<Call> $calls The calls.
     */
    public function setCalls(array $calls)
    {
        $this->spy->setCalls($calls);
    }

    /**
     * Add a call.
     *
     * @param Call $call The call.
     */
    public function addCall(Call $call)
    {
        $this->spy->addCall($call);
    }

    /**
     * Returns true if this collection contains any events.
     *
     * @return bool True if this collection contains any events.
     */
    public function hasEvents()
    {
        return $this->spy->hasEvents();
    }

    /**
     * Returns true if this collection contains any calls.
     *
     * @return bool True if this collection contains any calls.
     */
    public function hasCalls()
    {
        return $this->spy->hasCalls();
    }

    /**
     * Get the number of events.
     *
     * @return int The event count.
     */
    public function eventCount()
    {
        return $this->spy->eventCount();
    }

    /**
     * Get the number of calls.
     *
     * @return int The call count.
     */
    public function callCount()
    {
        return $this->spy->callCount();
    }

    /**
     * Get all events as an array.
     *
     * @return array<Event> The events.
     */
    public function allEvents()
    {
        return $this->spy->allEvents();
    }

    /**
     * Get all calls as an array.
     *
     * @return array<CallVerifier> The calls.
     */
    public function allCalls()
    {
        return $this->callVerifierFactory->fromCalls($this->spy->allCalls());
    }

    /**
     * Get the first event.
     *
     * @return Event                   The event.
     * @throws UndefinedEventException If there are no events.
     */
    public function firstEvent()
    {
        return $this->spy->firstEvent();
    }

    /**
     * Get the last event.
     *
     * @return Event                   The event.
     * @throws UndefinedEventException If there are no events.
     */
    public function lastEvent()
    {
        return $this->spy->lastEvent();
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
        return $this->spy->eventAt($index);
    }

    /**
     * Get the first call.
     *
     * @return Call                   The call.
     * @throws UndefinedCallException If there are no calls.
     */
    public function firstCall()
    {
        return $this->callVerifierFactory->fromCall($this->spy->firstCall());
    }

    /**
     * Get the last call.
     *
     * @return Call                   The call.
     * @throws UndefinedCallException If there are no calls.
     */
    public function lastCall()
    {
        return $this->callVerifierFactory->fromCall($this->spy->lastCall());
    }

    /**
     * Get a call by index.
     *
     * Negative indices are offset from the end of the list. That is, `-1`
     * indicates the last element, and `-2` indicates the second last element.
     *
     * @param int $index The index.
     *
     * @return CallVerifier           The call.
     * @throws UndefinedCallException If the requested call is undefined, or there are no calls.
     */
    public function callAt($index = 0)
    {
        return $this->callVerifierFactory->fromCall($this->spy->callAt($index));
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
     * Get the event count.
     *
     * @return int The event count.
     */
    public function count()
    {
        return $this->spy->count();
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
        return $this->spy->invokeWith($arguments);
    }

    /**
     * Invoke this object.
     *
     * @param mixed ...$arguments The arguments.
     *
     * @return mixed           The result of invocation.
     * @throws Exception|Error If an error occurs.
     */
    public function invoke()
    {
        return $this->spy->invokeWith(func_get_args());
    }

    /**
     * Invoke this object.
     *
     * @param mixed ...$arguments The arguments.
     *
     * @return mixed           The result of invocation.
     * @throws Exception|Error If an error occurs.
     */
    public function __invoke()
    {
        return $this->spy->invokeWith(func_get_args());
    }

    /**
     * Checks if called.
     *
     * @return EventCollection|null The result.
     */
    public function checkCalled()
    {
        $cardinality = $this->resetCardinality();

        $calls = $this->spy->allCalls();
        $callCount = count($calls);

        if ($cardinality->matches($callCount, $callCount)) {
            return $this->assertionRecorder->createSuccess($calls);
        }
    }

    /**
     * Throws an exception unless called.
     *
     * @return EventCollection The result.
     * @throws Exception       If the assertion fails, and the assertion recorder throws exceptions.
     */
    public function called()
    {
        $cardinality = $this->cardinality;

        if ($result = $this->checkCalled()) {
            return $result;
        }

        return $this->assertionRecorder->createFailure(
            $this->assertionRenderer->renderCalled($this->spy, $cardinality)
        );
    }

    /**
     * Checks if called with the supplied arguments.
     *
     * @param mixed ...$argument The arguments.
     *
     * @return EventCollection|null The result.
     */
    public function checkCalledWith()
    {
        $cardinality = $this->resetCardinality();

        $matchers = $this->matcherFactory->adaptAll(func_get_args());
        $calls = $this->spy->allCalls();
        $matchingEvents = array();
        $totalCount = count($calls);
        $matchCount = 0;

        foreach ($calls as $call) {
            if (
                $this->matcherVerifier
                    ->matches($matchers, $call->arguments()->all())
            ) {
                $matchingEvents[] = $call;
                ++$matchCount;
            }
        }

        if ($cardinality->matches($matchCount, $totalCount)) {
            return $this->assertionRecorder->createSuccess($matchingEvents);
        }
    }

    /**
     * Throws an exception unless called with the supplied arguments.
     *
     * @param mixed ...$argument The arguments.
     *
     * @return EventCollection The result.
     * @throws Exception       If the assertion fails, and the assertion recorder throws exceptions.
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
                ->renderCalledWith($this->spy, $cardinality, $matchers)
        );
    }

    /**
     * Checks if this spy responded.
     *
     * @return EventCollection|null The result.
     */
    public function checkResponded()
    {
        $cardinality = $this->resetCardinality();

        $calls = $this->spy->allCalls();
        $matchingEvents = array();
        $totalCount = count($calls);
        $matchCount = 0;

        foreach ($calls as $call) {
            if ($responseEvent = $call->responseEvent()) {
                $matchingEvents[] = $responseEvent;
                ++$matchCount;
            }
        }

        if ($cardinality->matches($matchCount, $totalCount)) {
            return $this->assertionRecorder->createSuccess($matchingEvents);
        }
    }

    /**
     * Throws an exception unless this spy responded.
     *
     * @return EventCollection The result.
     * @throws Exception       If the assertion fails, and the assertion recorder throws exceptions.
     */
    public function responded()
    {
        $cardinality = $this->cardinality;

        if ($result = $this->checkResponded()) {
            return $result;
        }

        return $this->assertionRecorder->createFailure(
            $this->assertionRenderer->renderResponded($this->spy, $cardinality)
        );
    }

    /**
     * Checks if this spy completed.
     *
     * @return EventCollection|null The result.
     */
    public function checkCompleted()
    {
        $cardinality = $this->resetCardinality();

        $calls = $this->spy->allCalls();
        $matchingEvents = array();
        $totalCount = count($calls);
        $matchCount = 0;

        foreach ($calls as $call) {
            if ($endEvent = $call->endEvent()) {
                $matchingEvents[] = $endEvent;
                ++$matchCount;
            }
        }

        if ($cardinality->matches($matchCount, $totalCount)) {
            return $this->assertionRecorder->createSuccess($matchingEvents);
        }
    }

    /**
     * Throws an exception unless this spy completed.
     *
     * @return EventCollection The result.
     * @throws Exception       If the assertion fails, and the assertion recorder throws exceptions.
     */
    public function completed()
    {
        $cardinality = $this->cardinality;

        if ($result = $this->checkCompleted()) {
            return $result;
        }

        return $this->assertionRecorder->createFailure(
            $this->assertionRenderer->renderCompleted($this->spy, $cardinality)
        );
    }

    /**
     * Checks if this spy returned the supplied value.
     *
     * @param mixed $value The value.
     *
     * @return EventCollection|null The result.
     */
    public function checkReturned($value = null)
    {
        $cardinality = $this->resetCardinality();

        $calls = $this->spy->allCalls();
        $matchingEvents = array();
        $totalCount = count($calls);
        $matchCount = 0;

        if (0 === func_num_args()) {
            foreach ($calls as $call) {
                if (!$responseEvent = $call->responseEvent()) {
                    continue;
                }

                list($exception) = $call->response();

                if (!$exception) {
                    $matchingEvents[] = $responseEvent;
                    ++$matchCount;
                }
            }
        } else {
            $value = $this->matcherFactory->adapt($value);

            foreach ($calls as $call) {
                if (!$responseEvent = $call->responseEvent()) {
                    continue;
                }

                list($exception, $returnValue) = $call->response();

                if (!$exception && $value->matches($returnValue)) {
                    $matchingEvents[] = $responseEvent;
                    ++$matchCount;
                }
            }
        }

        if ($cardinality->matches($matchCount, $totalCount)) {
            return $this->assertionRecorder->createSuccess($matchingEvents);
        }
    }

    /**
     * Throws an exception unless this spy returned the supplied value.
     *
     * @param mixed $value The value.
     *
     * @return EventCollection The result.
     * @throws Exception       If the assertion fails, and the assertion recorder throws exceptions.
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
                ->renderReturned($this->spy, $cardinality, $value)
        );
    }

    /**
     * Checks if an exception of the supplied type was thrown.
     *
     * @param Matcher|Exception|Error|string|null $type An exception to match, the type of exception, or null for any exception.
     *
     * @return EventCollection|null     The result.
     * @throws InvalidArgumentException If the type is invalid.
     */
    public function checkThrew($type = null)
    {
        $cardinality = $this->resetCardinality();

        $calls = $this->spy->allCalls();
        $matchingEvents = array();
        $totalCount = count($calls);
        $matchCount = 0;
        $isTypeSupported = false;

        if (!$type) {
            $isTypeSupported = true;

            foreach ($calls as $call) {
                if (!$responseEvent = $call->responseEvent()) {
                    continue;
                }

                list($exception) = $call->response();

                if ($exception) {
                    $matchingEvents[] = $responseEvent;
                    ++$matchCount;
                }
            }
        } elseif (is_string($type)) {
            $isTypeSupported = true;

            foreach ($calls as $call) {
                if (!$responseEvent = $call->responseEvent()) {
                    continue;
                }

                list($exception) = $call->response();

                if ($exception && is_a($exception, $type)) {
                    $matchingEvents[] = $responseEvent;
                    ++$matchCount;
                }
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
                foreach ($calls as $call) {
                    if (!$responseEvent = $call->responseEvent()) {
                        continue;
                    }

                    list($exception) = $call->response();

                    if ($exception && $type->matches($exception)) {
                        $matchingEvents[] = $responseEvent;
                        ++$matchCount;
                    }
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

        if ($cardinality->matches($matchCount, $totalCount)) {
            return $this->assertionRecorder->createSuccess($matchingEvents);
        }
    }

    /**
     * Throws an exception unless an exception of the supplied type was thrown.
     *
     * @param Matcher|Exception|Error|string|null $type An exception to match, the type of exception, or null for any exception.
     *
     * @return EventCollection          The result.
     * @throws InvalidArgumentException If the type is invalid.
     * @throws Exception                If the assertion fails, and the assertion recorder throws exceptions.
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
                ->renderThrew($this->spy, $cardinality, $type)
        );
    }

    /**
     * Checks if this spy returned a generator.
     *
     * @return GeneratorVerifier|null The result.
     */
    public function checkGenerated()
    {
        $cardinality = $this->resetCardinality();

        $calls = $this->spy->allCalls();
        $matchingEvents = array();
        $totalCount = count($calls);
        $matchCount = 0;

        foreach ($calls as $call) {
            if (!$call->responseEvent()) {
                continue;
            }

            list(, $returnValue) = $call->response();

            if ($returnValue instanceof Generator) {
                $matchingEvents[] = $call;
                ++$matchCount;
            }
        }

        if ($cardinality->matches($matchCount, $totalCount)) {
            return $this->assertionRecorder->createSuccessFromEventCollection(
                $this->generatorVerifierFactory
                    ->create($this->spy, $matchingEvents)
            );
        }
    }

    /**
     * Throws an exception unless this spy returned a generator.
     *
     * @return GeneratorVerifier The result.
     * @throws Exception         If the assertion fails, and the assertion recorder throws exceptions.
     */
    public function generated()
    {
        $cardinality = $this->cardinality;

        if ($result = $this->checkGenerated()) {
            return $result;
        }

        return $this->assertionRecorder->createFailure(
            $this->assertionRenderer->renderGenerated($this->spy, $cardinality)
        );
    }

    /**
     * Checks if this spy returned an iterable.
     *
     * @return IterableVerifier|null The result.
     */
    public function checkIterated()
    {
        $cardinality = $this->resetCardinality();

        $calls = $this->spy->allCalls();
        $matchingEvents = array();
        $totalCount = count($calls);
        $matchCount = 0;

        foreach ($calls as $call) {
            if (!$call->responseEvent()) {
                continue;
            }

            list(, $returnValue) = $call->response();

            if ($returnValue instanceof Traversable || is_array($returnValue)) {
                $matchingEvents[] = $call;
                ++$matchCount;
            }
        }

        if ($cardinality->matches($matchCount, $totalCount)) {
            return $this->assertionRecorder->createSuccessFromEventCollection(
                $this->iterableVerifierFactory
                    ->create($this->spy, $matchingEvents)
            );
        }
    }

    /**
     * Throws an exception unless this spy returned an iterable.
     *
     * @return IterableVerifier The result.
     * @throws Exception        If the assertion fails, and the assertion recorder throws exceptions.
     */
    public function iterated()
    {
        $cardinality = $this->cardinality;

        if ($result = $this->checkIterated()) {
            return $result;
        }

        return $this->assertionRecorder->createFailure(
            $this->assertionRenderer->renderIterated($this->spy, $cardinality)
        );
    }

    private $spy;
    private $matcherFactory;
    private $matcherVerifier;
    private $generatorVerifierFactory;
    private $iterableVerifierFactory;
    private $callVerifierFactory;
    private $assertionRecorder;
    private $assertionRenderer;
}

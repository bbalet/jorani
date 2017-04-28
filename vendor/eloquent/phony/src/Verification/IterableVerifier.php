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

use Eloquent\Phony\Assertion\AssertionRecorder;
use Eloquent\Phony\Assertion\AssertionRenderer;
use Eloquent\Phony\Call\Call;
use Eloquent\Phony\Call\CallVerifierFactory;
use Eloquent\Phony\Call\Event\ProducedEvent;
use Eloquent\Phony\Call\Event\UsedEvent;
use Eloquent\Phony\Event\EventCollection;
use Eloquent\Phony\Matcher\MatcherFactory;
use Eloquent\Phony\Spy\Spy;
use Exception;

/**
 * Checks and asserts the behavior of iterables.
 */
class IterableVerifier extends AbstractCardinalityVerifierEventCollection
{
    /**
     * Construct a new iterable verifier.
     *
     * @param Spy|Call            $subject             The subject.
     * @param array<Call>         $calls               The iterable calls.
     * @param MatcherFactory      $matcherFactory      The matcher factory to use.
     * @param CallVerifierFactory $callVerifierFactory The call verifier factory to use.
     * @param AssertionRecorder   $assertionRecorder   The assertion recorder to use.
     * @param AssertionRenderer   $assertionRenderer   The assertion renderer to use.
     */
    public function __construct(
        $subject,
        array $calls,
        MatcherFactory $matcherFactory,
        CallVerifierFactory $callVerifierFactory,
        AssertionRecorder $assertionRecorder,
        AssertionRenderer $assertionRenderer
    ) {
        $this->subject = $subject;
        $this->matcherFactory = $matcherFactory;
        $this->assertionRecorder = $assertionRecorder;
        $this->assertionRenderer = $assertionRenderer;
        $this->isGenerator = false;

        parent::__construct($calls, $callVerifierFactory);
    }

    /**
     * Checks if iteration of the subject commenced.
     *
     * @return EventCollection|null The result.
     */
    public function checkUsed()
    {
        $cardinality = $this->resetCardinality();

        if ($this->subject instanceof Call) {
            $cardinality->assertSingular();
        }

        $matchingEvents = array();
        $matchCount = 0;

        foreach ($this->calls as $call) {
            foreach ($call->iterableEvents() as $event) {
                if ($event instanceof UsedEvent) {
                    $matchingEvents[] = $event;
                    ++$matchCount;
                }
            }
        }

        if ($cardinality->matches($matchCount, $this->callCount)) {
            return $this->assertionRecorder->createSuccess($matchingEvents);
        }
    }

    /**
     * Throws an exception unless iteration of the subject commenced.
     *
     * @return EventCollection The result.
     * @throws Exception       If the assertion fails, and the assertion recorder throws exceptions.
     */
    public function used()
    {
        $cardinality = $this->cardinality;

        if ($result = $this->checkUsed()) {
            return $result;
        }

        return $this->assertionRecorder->createFailure(
            $this->assertionRenderer->renderIterableUsed(
                $this->subject,
                $cardinality,
                $this->isGenerator
            )
        );
    }

    /**
     * Checks if the subject produced the supplied values.
     *
     * When called with no arguments, this method simply checks that the subject
     * produced any value.
     *
     * With a single argument, it checks that a value matching the argument was
     * produced.
     *
     * With two arguments, it checks that a key and value matching the
     * respective arguments were produced together.
     *
     * @param mixed $keyOrValue The key or value.
     * @param mixed $value      The value.
     *
     * @return EventCollection|null The result.
     */
    public function checkProduced($keyOrValue = null, $value = null)
    {
        $cardinality = $this->resetCardinality();

        $argumentCount = func_num_args();

        if (0 === $argumentCount) {
            $checkKey = false;
            $checkValue = false;
        } elseif (1 === $argumentCount) {
            $checkKey = false;
            $checkValue = true;
            $value = $this->matcherFactory->adapt($keyOrValue);
        } else {
            $checkKey = true;
            $checkValue = true;
            $key = $this->matcherFactory->adapt($keyOrValue);
            $value = $this->matcherFactory->adapt($value);
        }

        $isCall = $this->subject instanceof Call;
        $matchingEvents = array();
        $matchCount = 0;
        $eventCount = 0;

        foreach ($this->calls as $call) {
            $isMatchingCall = false;

            foreach ($call->iterableEvents() as $event) {
                if ($event instanceof ProducedEvent) {
                    ++$eventCount;

                    if ($checkKey && !$key->matches($event->key())) {
                        continue;
                    }

                    if ($checkValue && !$value->matches($event->value())) {
                        continue;
                    }

                    $matchingEvents[] = $event;
                    $isMatchingCall = true;

                    if ($isCall) {
                        ++$matchCount;
                    }
                }
            }

            if (!$isCall && $isMatchingCall) {
                ++$matchCount;
            }
        }

        if ($isCall) {
            $totalCount = $eventCount;
        } else {
            $totalCount = $this->callCount;
        }

        if ($cardinality->matches($matchCount, $totalCount)) {
            return $this->assertionRecorder->createSuccess($matchingEvents);
        }
    }

    /**
     * Throws an exception unless the subject produced the supplied values.
     *
     * When called with no arguments, this method simply checks that the subject
     * produced any value.
     *
     * With a single argument, it checks that a value matching the argument was
     * produced.
     *
     * With two arguments, it checks that a key and value matching the
     * respective arguments were produced together.
     *
     * @param mixed $keyOrValue The key or value.
     * @param mixed $value      The value.
     *
     * @return EventCollection The result.
     * @throws Exception       If the assertion fails, and the assertion recorder throws exceptions.
     */
    public function produced($keyOrValue = null, $value = null)
    {
        $cardinality = $this->cardinality;
        $argumentCount = func_num_args();

        if (0 === $argumentCount) {
            $key = null;
            $arguments = array();
        } elseif (1 === $argumentCount) {
            $key = null;
            $value = $this->matcherFactory->adapt($keyOrValue);
            $arguments = array($value);
        } else {
            $key = $this->matcherFactory->adapt($keyOrValue);
            $value = $this->matcherFactory->adapt($value);
            $arguments = array($key, $value);
        }

        if (
            $result =
                call_user_func_array(array($this, 'checkProduced'), $arguments)
        ) {
            return $result;
        }

        return $this->assertionRecorder->createFailure(
            $this->assertionRenderer->renderIterableProduced(
                $this->subject,
                $cardinality,
                $this->isGenerator,
                $key,
                $value
            )
        );
    }

    /**
     * Checks if the subject was completely consumed.
     *
     * @return EventCollection|null The result.
     */
    public function checkConsumed()
    {
        $cardinality = $this->resetCardinality();

        if ($this->subject instanceof Call) {
            $cardinality->assertSingular();
        }

        $matchingEvents = array();
        $matchCount = 0;

        foreach ($this->calls as $call) {
            if (!$endEvent = $call->endEvent()) {
                continue;
            }
            if (!$call->isIterable()) {
                continue;
            }

            ++$matchCount;
            $matchingEvents[] = $endEvent;
        }

        if ($cardinality->matches($matchCount, $this->callCount)) {
            return $this->assertionRecorder->createSuccess($matchingEvents);
        }
    }

    /**
     * Throws an exception unless the subject was completely consumed.
     *
     * @return EventCollection The result.
     * @throws Exception       If the assertion fails, and the assertion recorder throws exceptions.
     */
    public function consumed()
    {
        $cardinality = $this->cardinality;

        if ($result = $this->checkConsumed()) {
            return $result;
        }

        return $this->assertionRecorder->createFailure(
            $this->assertionRenderer->renderIterableConsumed(
                $this->subject,
                $cardinality,
                $this->isGenerator
            )
        );
    }

    protected $subject;
    protected $matcherFactory;
    protected $assertionRecorder;
    protected $assertionRenderer;
    protected $isGenerator;
}

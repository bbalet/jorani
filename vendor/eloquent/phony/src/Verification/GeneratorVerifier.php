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
use Eloquent\Phony\Call\Event\ReceivedEvent;
use Eloquent\Phony\Call\Event\ReceivedExceptionEvent;
use Eloquent\Phony\Event\EventCollection;
use Eloquent\Phony\Matcher\Matcher;
use Eloquent\Phony\Matcher\MatcherFactory;
use Eloquent\Phony\Mock\Handle\InstanceHandle;
use Eloquent\Phony\Spy\Spy;
use Error;
use Exception;
use InvalidArgumentException;
use Throwable;

/**
 * Checks and asserts the behavior of generators.
 */
class GeneratorVerifier extends IterableVerifier
{
    /**
     * Construct a new generator verifier.
     *
     * @param Spy|Call            $subject             The subject.
     * @param array<Call>         $calls               The generator calls.
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
        parent::__construct(
            $subject,
            $calls,
            $matcherFactory,
            $callVerifierFactory,
            $assertionRecorder,
            $assertionRenderer
        );

        $this->isGenerator = true;
    }

    /**
     * Checks if the subject received the supplied value.
     *
     * When called with no arguments, this method simply checks that the subject
     * received any value.
     *
     * @param mixed $value The value.
     *
     * @return EventCollection|null The result.
     */
    public function checkReceived($value = null)
    {
        $cardinality = $this->resetCardinality();

        $argumentCount = func_num_args();

        if (0 === $argumentCount) {
            $checkValue = false;
        } else {
            $checkValue = true;
            $value = $this->matcherFactory->adapt($value);
        }

        $isCall = $this->subject instanceof Call;
        $matchingEvents = array();
        $matchCount = 0;
        $eventCount = 0;

        foreach ($this->calls as $call) {
            $isMatchingCall = false;

            foreach ($call->iterableEvents() as $event) {
                if ($event instanceof ReceivedEvent) {
                    ++$eventCount;

                    if (!$checkValue || $value->matches($event->value())) {
                        $matchingEvents[] = $event;
                        $isMatchingCall = true;

                        if ($isCall) {
                            ++$matchCount;
                        }
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
     * Throws an exception unless the subject received the supplied value.
     *
     * When called with no arguments, this method simply checks that the subject
     * received any value.
     *
     * @param mixed $value The value.
     *
     * @return EventCollection The result.
     * @throws Exception       If the assertion fails, and the assertion recorder throws exceptions.
     */
    public function received($value = null)
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
                call_user_func_array(array($this, 'checkReceived'), $arguments)
        ) {
            return $result;
        }

        return $this->assertionRecorder->createFailure(
            $this->assertionRenderer
                ->renderGeneratorReceived($this->subject, $cardinality, $value)
        );
    }

    /**
     * Checks if the subject received an exception of the supplied type.
     *
     * @param Matcher|Exception|Error|string|null $type An exception to match, the type of exception, or null for any exception.
     *
     * @return EventCollection|null     The result.
     * @throws InvalidArgumentException If the type is invalid.
     */
    public function checkReceivedException($type = null)
    {
        $cardinality = $this->resetCardinality();

        $isCall = $this->subject instanceof Call;
        $matchingEvents = array();
        $matchCount = 0;
        $eventCount = 0;
        $isTypeSupported = false;

        if (!$type) {
            $isTypeSupported = true;

            foreach ($this->calls as $call) {
                $isMatchingCall = false;

                foreach ($call->iterableEvents() as $event) {
                    if ($event instanceof ReceivedExceptionEvent) {
                        ++$eventCount;

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
        } elseif (is_string($type)) {
            $isTypeSupported = true;

            foreach ($this->calls as $call) {
                $isMatchingCall = false;

                foreach ($call->iterableEvents() as $event) {
                    if ($event instanceof ReceivedExceptionEvent) {
                        ++$eventCount;

                        if (is_a($event->exception(), $type)) {
                            $matchingEvents[] = $event;
                            $isMatchingCall = true;

                            if ($isCall) {
                                ++$matchCount;
                            }
                        }
                    }
                }

                if (!$isCall && $isMatchingCall) {
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
                foreach ($this->calls as $call) {
                    $isMatchingCall = false;

                    foreach ($call->iterableEvents() as $event) {
                        if ($event instanceof ReceivedExceptionEvent) {
                            ++$eventCount;

                            if ($type->matches($event->exception())) {
                                $matchingEvents[] = $event;
                                $isMatchingCall = true;

                                if ($isCall) {
                                    ++$matchCount;
                                }
                            }
                        }
                    }

                    if (!$isCall && $isMatchingCall) {
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
     * Throws an exception unless the subject received an exception of the
     * supplied type.
     *
     * @param Matcher|Exception|Error|string|null $type An exception to match, the type of exception, or null for any exception.
     *
     * @return EventCollection          The result.
     * @throws InvalidArgumentException If the type is invalid.
     * @throws Exception                If the assertion fails, and the assertion recorder throws exceptions.
     */
    public function receivedException($type = null)
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

        if ($result = $this->checkReceivedException($type)) {
            return $result;
        }

        return $this->assertionRecorder->createFailure(
            $this->assertionRenderer->renderGeneratorReceivedException(
                $this->subject,
                $cardinality,
                $type
            )
        );
    }

    /**
     * Checks if the subject returned the supplied value from a generator.
     *
     * @param mixed $value The value.
     *
     * @return EventCollection|null The result.
     */
    public function checkReturned($value = null)
    {
        $cardinality = $this->resetCardinality();

        if ($this->subject instanceof Call) {
            $cardinality->assertSingular();
        }

        $matchingEvents = array();
        $matchCount = 0;

        if (0 === func_num_args()) {
            foreach ($this->calls as $call) {
                if (!$call->isGenerator() || !$endEvent = $call->endEvent()) {
                    continue;
                }

                list($exception) = $call->generatorResponse();

                if (!$exception) {
                    $matchingEvents[] = $endEvent;
                    ++$matchCount;
                }
            }
        } else {
            $value = $this->matcherFactory->adapt($value);

            foreach ($this->calls as $call) {
                if (!$call->isGenerator() || !$endEvent = $call->endEvent()) {
                    continue;
                }

                list($exception, $returnValue) = $call->generatorResponse();

                if (!$exception && $value->matches($returnValue)) {
                    $matchingEvents[] = $endEvent;
                    ++$matchCount;
                }
            }
        }

        if ($cardinality->matches($matchCount, $this->callCount)) {
            return $this->assertionRecorder->createSuccess($matchingEvents);
        }
    }

    /**
     * Throws an exception unless the subject returned the supplied value from a
     * generator.
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
                ->renderGeneratorReturned($this->subject, $cardinality, $value)
        );
    }

    /**
     * Checks if an exception of the supplied type was thrown from a generator.
     *
     * @param Matcher|Exception|Error|string|null $type An exception to match, the type of exception, or null for any exception.
     *
     * @return EventCollection|null     The result.
     * @throws InvalidArgumentException If the type is invalid.
     */
    public function checkThrew($type = null)
    {
        $cardinality = $this->resetCardinality();

        if ($this->subject instanceof Call) {
            $cardinality->assertSingular();
        }

        $matchingEvents = array();
        $matchCount = 0;
        $isTypeSupported = false;

        if (!$type) {
            $isTypeSupported = true;

            foreach ($this->calls as $call) {
                if (!$call->isGenerator() || !$endEvent = $call->endEvent()) {
                    continue;
                }

                list($exception) = $call->generatorResponse();

                if ($exception) {
                    $matchingEvents[] = $endEvent;
                    ++$matchCount;
                }
            }
        } elseif (is_string($type)) {
            $isTypeSupported = true;

            foreach ($this->calls as $call) {
                if (!$call->isGenerator() || !$endEvent = $call->endEvent()) {
                    continue;
                }

                list($exception) = $call->generatorResponse();

                if ($exception && is_a($exception, $type)) {
                    $matchingEvents[] = $endEvent;
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
                foreach ($this->calls as $call) {
                    if (
                        !$call->isGenerator() ||
                        !$endEvent = $call->endEvent()
                    ) {
                        continue;
                    }

                    list($exception) = $call->generatorResponse();

                    if ($exception && $type->matches($exception)) {
                        $matchingEvents[] = $endEvent;
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

        if ($cardinality->matches($matchCount, $this->callCount)) {
            return $this->assertionRecorder->createSuccess($matchingEvents);
        }
    }

    /**
     * Throws an exception unless an exception of the supplied type was thrown
     * from a generator.
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
                ->renderGeneratorThrew($this->subject, $cardinality, $type)
        );
    }
}

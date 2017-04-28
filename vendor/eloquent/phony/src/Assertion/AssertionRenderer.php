<?php

/*
 * This file is part of the Phony package.
 *
 * Copyright © 2016 Erin Millard
 *
 * For the full copyright and license information, please view the LICENSE file
 * that was distributed with this source code.
 */

namespace Eloquent\Phony\Assertion;

use Eloquent\Phony\Call\Call;
use Eloquent\Phony\Call\Event\CalledEvent;
use Eloquent\Phony\Call\Event\CallEvent;
use Eloquent\Phony\Call\Event\ConsumedEvent;
use Eloquent\Phony\Call\Event\ProducedEvent;
use Eloquent\Phony\Call\Event\ReceivedEvent;
use Eloquent\Phony\Call\Event\ReceivedExceptionEvent;
use Eloquent\Phony\Call\Event\ReturnedEvent;
use Eloquent\Phony\Call\Event\ThrewEvent;
use Eloquent\Phony\Call\Event\UsedEvent;
use Eloquent\Phony\Difference\DifferenceEngine;
use Eloquent\Phony\Difference\DifferenceSequenceMatcher;
use Eloquent\Phony\Exporter\Exporter;
use Eloquent\Phony\Exporter\InlineExporter;
use Eloquent\Phony\Matcher\AnyMatcher;
use Eloquent\Phony\Matcher\EqualToMatcher;
use Eloquent\Phony\Matcher\Matchable;
use Eloquent\Phony\Matcher\Matcher;
use Eloquent\Phony\Matcher\MatcherVerifier;
use Eloquent\Phony\Matcher\WildcardMatcher;
use Eloquent\Phony\Mock\Handle\Handle;
use Eloquent\Phony\Mock\Handle\InstanceHandle;
use Eloquent\Phony\Reflection\FeatureDetector;
use Eloquent\Phony\Spy\Spy;
use Eloquent\Phony\Verification\Cardinality;
use Error;
use Exception;
use Generator;
use Traversable;

/**
 * Renders various data for use in assertion messages.
 */
class AssertionRenderer
{
    /**
     * Get the static instance of this renderer.
     *
     * @return AssertionRenderer The static renderer.
     */
    public static function instance()
    {
        if (!self::$instance) {
            self::$instance = new self(
                MatcherVerifier::instance(),
                InlineExporter::instance(),
                DifferenceEngine::instance(),
                FeatureDetector::instance()
            );
        }

        return self::$instance;
    }

    /**
     * Construct a new call renderer.
     *
     * @param MatcherVerifier  $matcherVerifier  The matcher verifier to use.
     * @param Exporter         $exporter         The exporter to use.
     * @param DifferenceEngine $differenceEngine The difference engine to use.
     * @param FeatureDetector  $featureDetector  The feature detector to use.
     */
    public function __construct(
        MatcherVerifier $matcherVerifier,
        Exporter $exporter,
        DifferenceEngine $differenceEngine,
        FeatureDetector $featureDetector
    ) {
        $this->matcherVerifier = $matcherVerifier;
        $this->exporter = $exporter;
        $this->differenceEngine = $differenceEngine;
        $this->featureDetector = $featureDetector;

        $this->setUseColor(null);
    }

    /**
     * Turn on or off the use of ANSI colored output.
     *
     * Pass `null` to detect automatically.
     *
     * @param bool|null $useColor True to use color.
     */
    public function setUseColor($useColor)
    {
        if (null === $useColor) {
            $useColor = $this->featureDetector->isSupported('stdout.ansi');
        }

        if ($useColor) {
            $this->reset = "\033[0m";
            $this->bold = "\033[1m";
            $this->faint = "\033[2m";
            $this->passStart = "\033[32m";
            $this->failStart = "\033[31m";
            $this->pass = $this->passStart . self::PASS . $this->reset;
            $this->fail = $this->failStart . self::FAIL . $this->reset;

            $this->addStart = "\033[33m\033[2m{+\033[0m\033[33m";
            $this->addEnd = "\033[2m+}\033[0m";
            $this->removeStart = "\033[36m\033[2m[-\033[0m\033[36m";
            $this->removeEnd = "\033[2m-]\033[0m";
        } else {
            $this->reset = '';
            $this->bold = '';
            $this->faint = '';
            $this->passStart = '';
            $this->failStart = '';
            $this->pass = self::PASS;
            $this->fail = self::FAIL;

            $this->addStart = '{+';
            $this->addEnd = '+}';
            $this->removeStart = '[-';
            $this->removeEnd = '-]';
        }
    }

    /**
     * Render a failed called() verification.
     *
     * @param Spy|Call    $subject     The subject.
     * @param Cardinality $cardinality The cardinality.
     *
     * @return string The rendered failure message.
     */
    public function renderCalled($subject, Cardinality $cardinality)
    {
        $isCall = $subject instanceof Call;

        if ($isCall) {
            $calls = array($subject);
            $renderedSubject =
                $this->bold .
                $this->exporter->exportCallable($subject->callback()) .
                $this->reset;
        } else {
            $calls = $subject->allCalls();
            $renderedSubject =
                $this->bold .
                $this->exporter->exportCallable($subject) .
                $this->reset;
        }

        $minimum = $cardinality->minimum();
        $maximum = $cardinality->maximum();
        $isNever = null !== $maximum && $maximum < 1;

        if ($isNever) {
            $expected = 'Expected no ' . $renderedSubject . ' call.';
        } else {
            $expected =
                'Expected ' . $renderedSubject . ' call with any arguments.';
        }

        $totalCount = count($calls);
        $matchCount = $totalCount;

        if ($totalCount) {
            if ($isNever) {
                $renderedResult = $this->fail;
            } else {
                $renderedResult = $this->pass;
            }

            $renderedCalls = array();

            foreach ($calls as $call) {
                $renderedArguments = array();

                foreach ($call->arguments()->all() as $argument) {
                    $renderedArguments[] =
                        '    ' . $renderedResult .
                        ' ' . $this->exporter->export($argument);
                }

                if (empty($renderedArguments)) {
                    $renderedArgumentList = ' (no arguments)';
                } else {
                    $renderedArgumentList =
                        ':' . PHP_EOL . implode(PHP_EOL, $renderedArguments);
                }

                $renderedCalls[] =
                    $renderedResult .
                    ' Call #' .
                    $call->index() .
                    $renderedArgumentList;
            }

            $actual = PHP_EOL . implode(PHP_EOL, $renderedCalls);
        } else {
            $actual = '';
        }

        $cardinality = $this->renderCardinality(
            $minimum,
            $maximum,
            $matchCount,
            $totalCount,
            $totalCount,
            true
        );

        return $this->reset . $expected . $cardinality . $actual;
    }

    /**
     * Render a failed calledWith() verification.
     *
     * @param Spy|Call         $subject     The subject.
     * @param Cardinality      $cardinality The cardinality.
     * @param array<Matchable> $matchers    The matchers.
     *
     * @return string The rendered failure message.
     */
    public function renderCalledWith(
        $subject,
        Cardinality $cardinality,
        array $matchers
    ) {
        $matcherCount = count($matchers);

        if (
            1 === $matcherCount &&
            $matchers[0] instanceof WildcardMatcher &&
            $matchers[0]->matcher() instanceof AnyMatcher &&
            0 === $matchers[0]->minimumArguments() &&
            null === $matchers[0]->maximumArguments()
        ) {
            return $this->renderCalled($subject, $cardinality);
        }

        $isCall = $subject instanceof Call;

        if ($isCall) {
            $calls = array($subject);
            $renderedSubject =
                $this->bold .
                $this->exporter->exportCallable($subject->callback()) .
                $this->reset;
        } else {
            $calls = $subject->allCalls();
            $renderedSubject =
                $this->bold .
                $this->exporter->exportCallable($subject) .
                $this->reset;
        }

        if ($matcherCount > 0) {
            $matcherMatchCounts = array_fill(0, $matcherCount, 0);
        } else {
            $matcherMatchCounts = array();
        }

        $callResults = array();
        $totalCount = 0;
        $matchCount = 0;

        foreach ($calls as $call) {
            ++$totalCount;

            $callResult = $this->matcherVerifier
                ->explain($matchers, $call->arguments()->all());

            if ($callResult->isMatch) {
                ++$matchCount;
            }

            foreach ($callResult->matcherMatches as $index => $isMatch) {
                if ($isMatch) {
                    ++$matcherMatchCounts[$index];
                }
            }

            $callResults[] = $callResult;
        }

        $renderedMatchers = array();
        $requiredArgumentCount = 0;

        foreach ($matchers as $index => $matcher) {
            if ($matcher instanceof WildcardMatcher) {
                $requiredArgumentCount += $matcher->minimumArguments();
            } else {
                ++$requiredArgumentCount;
            }

            if (
                $cardinality->matches($matcherMatchCounts[$index], $totalCount)
            ) {
                $resultText = self::PASS;
                $resultStart = $this->passStart;
            } else {
                $resultText = self::FAIL;
                $resultStart = $this->failStart;
            }

            if ($isCall) {
                $matcherMatchCount = '';
            } else {
                $matchOrMatches =
                    1 === $matcherMatchCounts[$index] ? 'match' : 'matches';
                $matcherMatchCount =
                    ' ' . $resultStart .
                    $this->faint .
                    '(' . $matcherMatchCounts[$index] .
                    ' ' . $matchOrMatches .
                    ')' . $this->reset;
            }

            $renderedMatchers[] =
                '    ' . $resultStart .
                $resultText .
                $this->reset .
                ' ' . $matcher->describe($this->exporter) .
                $matcherMatchCount;
        }

        if (empty($renderedMatchers)) {
            $renderedCriteria = 'no arguments.';
        } else {
            $renderedCriteria =
                'arguments:' . PHP_EOL . implode(PHP_EOL, $renderedMatchers);
        }

        $minimum = $cardinality->minimum();
        $maximum = $cardinality->maximum();
        $isNever = null !== $maximum && $maximum < 1;

        if ($isCall) {
            if ($isNever) {
                $expected =
                    'Expected ' . $renderedSubject .
                    ' call #' . $subject->index() .
                    ' not to have ' . $renderedCriteria;
            } else {
                $expected =
                    'Expected ' . $renderedSubject .
                    ' call #' . $subject->index() .
                    ' to have ' . $renderedCriteria;
            }
        } else {
            if ($isNever) {
                $expected =
                    'Expected no ' . $renderedSubject .
                    ' call with ' . $renderedCriteria;
            } elseif ($cardinality->isAlways()) {
                $expected =
                    'Expected all ' . $renderedSubject .
                    ' calls to have ' . $renderedCriteria;
            } else {
                $expected =
                    'Expected ' . $renderedSubject .
                    ' call with ' . $renderedCriteria;
            }
        }

        if ($totalCount) {
            $renderedCalls = array();

            foreach ($calls as $callIndex => $call) {
                $callResult = $callResults[$callIndex];
                $arguments = $call->arguments();
                $renderedArguments = array();

                if (count($arguments)) {
                    foreach (
                        $callResult->argumentMatches as
                            $argumentIndex => $isMatch
                    ) {
                        if ($isMatch xor $isNever) {
                            $renderedResult = $this->pass;
                        } else {
                            $renderedResult = $this->fail;
                        }

                        $exists = $arguments->has($argumentIndex);

                        if ($exists) {
                            $argument = $arguments->get($argumentIndex);
                            $value = $this->exporter->export($argument);

                            if (
                                !$isMatch &&
                                isset($matchers[$argumentIndex]) &&
                                $matchers[$argumentIndex] instanceof
                                    EqualToMatcher
                            ) {
                                $value = $this->differenceEngine->difference(
                                    $matchers[$argumentIndex]
                                        ->describe($this->exporter),
                                    $value
                                );
                            }
                        } else {
                            $value =
                                '<' .
                                ($requiredArgumentCount - $argumentIndex) .
                                ' missing>';
                        }

                        $renderedArguments[] =
                            '    ' . $renderedResult . ' ' . $value;

                        if (!$exists) {
                            break;
                        }
                    }
                }

                if ($callResult->isMatch xor $isNever) {
                    $renderedResult = $this->pass;
                } else {
                    $renderedResult = $this->fail;
                }

                if (empty($renderedArguments)) {
                    $renderedArgumentList = ' (no arguments)';
                } else {
                    $renderedArgumentList =
                        ':' . PHP_EOL . implode(PHP_EOL, $renderedArguments);
                }

                $renderedCalls[] =
                    $renderedResult .
                    ' Call #' .
                    $call->index() .
                    $renderedArgumentList;
            }

            $actual = PHP_EOL . implode(PHP_EOL, $renderedCalls);
        } else {
            $actual = '';
        }

        if ($isCall) {
            $cardinality = '';
        } else {
            $cardinality = $this->renderCardinality(
                $minimum,
                $maximum,
                $matchCount,
                $totalCount,
                $totalCount,
                false
            );
        }

        return $this->reset . $expected . $cardinality . $actual;
    }

    /**
     * Render a failed responded() verification.
     *
     * @param Spy|Call    $subject     The subject.
     * @param Cardinality $cardinality The cardinality.
     *
     * @return string The rendered failure message.
     */
    public function renderResponded($subject, Cardinality $cardinality)
    {
        $isCall = $subject instanceof Call;

        if ($isCall) {
            $calls = array($subject);
            $renderedCallee =
                $this->exporter->exportCallable($subject->callback());
        } else {
            $calls = $subject->allCalls();
            $renderedCallee = $this->exporter->exportCallable($subject);
        }

        $renderedSubject = $this->bold . $renderedCallee . $this->reset;

        $minimum = $cardinality->minimum();
        $maximum = $cardinality->maximum();
        $isNever = null !== $maximum && $maximum < 1;

        if ($isCall) {
            if ($isNever) {
                $expected =
                    'Expected ' . $renderedSubject .
                    ' call #' . $subject->index() . ' not to respond.';
            } else {
                $expected =
                    'Expected ' . $renderedSubject .
                    ' call #' . $subject->index() . ' to respond.';
            }
        } else {
            if ($isNever) {
                $expected = 'Expected ' . $renderedSubject . ' not to respond.';
            } elseif ($cardinality->isAlways()) {
                $expected =
                    'Expected all ' . $renderedSubject . ' calls to respond.';
            } else {
                $expected = 'Expected ' . $renderedSubject . ' to respond.';
            }
        }

        $totalCount = count($calls);
        $matchCount = 0;

        if ($totalCount) {
            $renderedCalls = array();

            foreach ($calls as $call) {
                $responseEvent = $call->responseEvent();

                if ($responseEvent) {
                    ++$matchCount;
                }

                if ($responseEvent xor $isNever) {
                    $renderedResult = $this->pass;
                } else {
                    $renderedResult = $this->fail;
                }

                $renderedArguments = array();

                foreach ($call->arguments()->all() as $argument) {
                    $renderedArguments[] =
                        $this->exporter->export($argument, 0);
                }

                if ($responseEvent instanceof ReturnedEvent) {
                    $returnValue = $responseEvent->value();
                    $renderedResponse =
                        'Returned ' . $this->exporter->export($returnValue);
                } elseif ($responseEvent instanceof ThrewEvent) {
                    $exception = $responseEvent->exception();
                    $renderedResponse =
                        'Threw ' . $this->exporter->export($exception);
                } else {
                    $renderedResponse = 'Never responded';
                }

                $renderedCalls[] =
                    $renderedResult .
                    ' Call #' . $call->index() .
                    ' - ' . $renderedCallee .
                    '(' . implode(', ', $renderedArguments) . '):' .
                    PHP_EOL . '    ' . $renderedResult .
                    ' ' . $renderedResponse;
            }

            $actual = PHP_EOL . implode(PHP_EOL, $renderedCalls);
        } else {
            $actual = '';
        }

        if ($isCall) {
            $cardinality = '';
        } else {
            $cardinality = $this->renderCardinality(
                $minimum,
                $maximum,
                $matchCount,
                $totalCount,
                $totalCount,
                false
            );
        }

        return $this->reset . $expected . $cardinality . $actual;
    }

    /**
     * Render a failed completed() verification.
     *
     * @param Spy|Call    $subject     The subject.
     * @param Cardinality $cardinality The cardinality.
     *
     * @return string The rendered failure message.
     */
    public function renderCompleted($subject, Cardinality $cardinality)
    {
        $isCall = $subject instanceof Call;

        if ($isCall) {
            $calls = array($subject);
            $renderedCallee =
                $this->exporter->exportCallable($subject->callback());
        } else {
            $calls = $subject->allCalls();
            $renderedCallee = $this->exporter->exportCallable($subject);
        }

        $renderedSubject = $this->bold . $renderedCallee . $this->reset;

        $minimum = $cardinality->minimum();
        $maximum = $cardinality->maximum();
        $isNever = null !== $maximum && $maximum < 1;

        if ($isCall) {
            if ($isNever) {
                $expected =
                    'Expected ' . $renderedSubject .
                    ' call #' . $subject->index() . ' not to complete.';
            } else {
                $expected =
                    'Expected ' . $renderedSubject .
                    ' call #' . $subject->index() . ' to complete.';
            }
        } else {
            if ($isNever) {
                $expected =
                    'Expected ' . $renderedSubject . ' not to complete.';
            } elseif ($cardinality->isAlways()) {
                $expected =
                    'Expected all ' . $renderedSubject . ' calls to complete.';
            } else {
                $expected =
                    'Expected ' . $renderedSubject . ' to complete.';
            }
        }

        $totalCount = count($calls);
        $matchCount = 0;

        if ($totalCount) {
            $renderedCalls = array();

            foreach ($calls as $call) {
                $endEvent = $call->endEvent();

                if ($endEvent) {
                    ++$matchCount;
                }

                if ($endEvent xor $isNever) {
                    $renderedResult = $this->pass;
                } else {
                    $renderedResult = $this->fail;
                }

                $renderedArguments = array();

                foreach ($call->arguments()->all() as $argument) {
                    $renderedArguments[] =
                        $this->exporter->export($argument, 0);
                }

                $responseEvent = $call->responseEvent();

                if ($responseEvent instanceof ReturnedEvent) {
                    $returnValue = $responseEvent->value();

                    if (
                        is_array($returnValue) ||
                        $returnValue instanceof Traversable
                    ) {
                        $iterableEvents = $call->iterableEvents();
                        $renderedIterableEvents = array();

                        foreach ($iterableEvents as $event) {
                            if ($event instanceof UsedEvent) {
                                $renderedIterableEvents[] =
                                    '        - Started iterating';
                            } elseif ($event instanceof ProducedEvent) {
                                $iterableKey = $event->key();
                                $iterableValue = $event->value();

                                $renderedIterableEvents[] =
                                    '        - Produced ' .
                                    $this->exporter->export($iterableKey) .
                                    ' => ' .
                                    $this->exporter->export($iterableValue);
                            } elseif ($event instanceof ReceivedEvent) {
                                $iterableValue = $event->value();

                                $renderedIterableEvents[] =
                                    '        - Received ' .
                                    $this->exporter->export($iterableValue);
                            } elseif (
                                $event instanceof ReceivedExceptionEvent
                            ) {
                                $iterableException = $event->exception();

                                $renderedIterableEvents[] =
                                    '        - Received exception ' .
                                    $this->exporter
                                        ->export($iterableException);
                            }
                        }

                        if (empty($iterableEvents)) {
                            $renderedIterableEvents[] =
                                '        ' . $renderedResult .
                                ' Never started iterating';
                        } elseif ($endEvent instanceof ConsumedEvent) {
                            $renderedIterableEvents[] =
                                '        ' . $renderedResult .
                                ' Finished iterating';
                        } elseif ($endEvent instanceof ReturnedEvent) {
                            $eventValue = $endEvent->value();

                            $renderedIterableEvents[] =
                                '        ' . $renderedResult . ' Returned ' .
                                $this->exporter->export($eventValue);
                        } elseif ($endEvent instanceof ThrewEvent) {
                            $eventException = $endEvent->exception();

                            $renderedIterableEvents[] =
                                '        ' . $renderedResult . ' Threw ' .
                                $this->exporter->export($eventException);
                        } else {
                            $renderedIterableEvents[] =
                                '        ' . $renderedResult .
                                ' Never finished iterating';
                        }

                        $renderedResponse =
                            'Returned ' .
                            $this->exporter->export($returnValue, 0) .
                            ', then:' . PHP_EOL .
                            implode(PHP_EOL, $renderedIterableEvents);
                    } else {
                        $renderedResponse =
                            'Returned ' . $this->exporter->export($returnValue);
                    }
                } elseif ($responseEvent instanceof ThrewEvent) {
                    $exception = $responseEvent->exception();
                    $renderedResponse =
                        'Threw ' . $this->exporter->export($exception);
                } else {
                    $renderedResponse = 'Never responded';
                }

                $renderedCalls[] =
                    $renderedResult .
                    ' Call #' . $call->index() .
                    ' - ' . $renderedCallee .
                    '(' . implode(', ', $renderedArguments) . '):' .
                    PHP_EOL . '    ' . $renderedResult .
                    ' ' . $renderedResponse;
            }

            $actual = PHP_EOL . implode(PHP_EOL, $renderedCalls);
        } else {
            $actual = '';
        }

        if ($isCall) {
            $cardinality = '';
        } else {
            $cardinality = $this->renderCardinality(
                $minimum,
                $maximum,
                $matchCount,
                $totalCount,
                $totalCount,
                false
            );
        }

        return $this->reset . $expected . $cardinality . $actual;
    }

    /**
     * Render a failed responded() verification.
     *
     * @param Spy|Call     $subject     The subject.
     * @param Cardinality  $cardinality The cardinality.
     * @param Matcher|null $value       The value.
     *
     * @return string The rendered failure message.
     */
    public function renderReturned(
        $subject,
        Cardinality $cardinality,
        Matcher $value = null
    ) {
        $isCall = $subject instanceof Call;

        if ($isCall) {
            $calls = array($subject);
            $renderedCallee =
                $this->exporter->exportCallable($subject->callback());
        } else {
            $calls = $subject->allCalls();
            $renderedCallee = $this->exporter->exportCallable($subject);
        }

        $renderedSubject = $this->bold . $renderedCallee . $this->reset;

        if ($value) {
            $renderedValue = $value->describe($this->exporter);
        } else {
            $renderedValue = '<any>';
        }

        $renderedCriteria =
            'behave like:' . PHP_EOL .
            '    ' . $this->fail . ' Returned ' . $renderedValue;

        $minimum = $cardinality->minimum();
        $maximum = $cardinality->maximum();
        $isNever = null !== $maximum && $maximum < 1;

        if ($isCall) {
            if ($isNever) {
                $expected =
                    'Expected ' . $renderedSubject .
                    ' call #' . $subject->index() .
                    ' not to ' . $renderedCriteria;
            } else {
                $expected =
                    'Expected ' . $renderedSubject .
                    ' call #' . $subject->index() .
                    ' to ' . $renderedCriteria;
            }
        } else {
            if ($isNever) {
                $expected =
                    'Expected ' . $renderedSubject .
                    ' not to ' . $renderedCriteria;
            } elseif ($cardinality->isAlways()) {
                $expected =
                    'Expected all ' . $renderedSubject .
                    ' calls to ' . $renderedCriteria;
            } else {
                $expected =
                    'Expected ' . $renderedSubject .
                    ' to ' . $renderedCriteria;
            }
        }

        $totalCount = count($calls);
        $matchCount = 0;

        if ($totalCount) {
            $renderedCalls = array();

            foreach ($calls as $call) {
                if ($responseEvent = $call->responseEvent()) {
                    list($exception, $returnValue) = $call->response();
                }

                if ($responseEvent instanceof ReturnedEvent) {
                    if ($value) {
                        $isMatch = $value->matches($returnValue);
                    } else {
                        $isMatch = true;
                    }
                } else {
                    $isMatch = false;
                }

                if ($isMatch) {
                    ++$matchCount;
                }

                if ($isMatch xor $isNever) {
                    $renderedResult = $this->pass;
                } else {
                    $renderedResult = $this->fail;
                }

                $renderedArguments = array();

                foreach ($call->arguments()->all() as $argument) {
                    $renderedArguments[] =
                        $this->exporter->export($argument, 0);
                }

                if ($responseEvent instanceof ReturnedEvent) {
                    $renderedReturnValue =
                        $this->exporter->export($returnValue);

                    if (!$isMatch && $value instanceof EqualToMatcher) {
                        $renderedReturnValue = $this->differenceEngine
                            ->difference($renderedValue, $renderedReturnValue);
                    }

                    $renderedResponse = 'Returned ' . $renderedReturnValue;
                } elseif ($responseEvent instanceof ThrewEvent) {
                    $renderedResponse =
                        'Threw ' . $this->exporter->export($exception);
                } else {
                    $renderedResponse = 'Never responded';
                }

                $renderedCalls[] =
                    $renderedResult . ' Call #' . $call->index() .
                    ' - ' . $renderedCallee .
                    '(' . implode(', ', $renderedArguments) . '):' .
                    PHP_EOL . '    ' . $renderedResult .
                    ' ' . $renderedResponse;
            }

            $actual = PHP_EOL . implode(PHP_EOL, $renderedCalls);
        } else {
            $actual = '';
        }

        if ($isCall) {
            $cardinality = '';
        } else {
            $cardinality = $this->renderCardinality(
                $minimum,
                $maximum,
                $matchCount,
                $totalCount,
                $totalCount,
                false
            );
        }

        return $this->reset . $expected . $cardinality . $actual;
    }

    /**
     * Render a failed threw() verification.
     *
     * @param Spy|Call            $subject     The subject.
     * @param Cardinality         $cardinality The cardinality.
     * @param Matcher|string|null $type        The type of exception.
     *
     * @return string The rendered failure message.
     */
    public function renderThrew($subject, Cardinality $cardinality, $type)
    {
        $isCall = $subject instanceof Call;

        if ($isCall) {
            $calls = array($subject);
            $renderedCallee =
                $this->exporter->exportCallable($subject->callback());
        } else {
            $calls = $subject->allCalls();
            $renderedCallee = $this->exporter->exportCallable($subject);
        }

        $renderedSubject = $this->bold . $renderedCallee . $this->reset;

        if ($type instanceof Matcher) {
            $renderedType = $type->describe($this->exporter);
        } elseif (is_string($type)) {
            $renderedType = $type;
        } else {
            $renderedType = '<any>';
        }

        $renderedCriteria =
            'behave like:' . PHP_EOL .
            '    ' . $this->fail . ' Threw ' . $renderedType;

        $minimum = $cardinality->minimum();
        $maximum = $cardinality->maximum();
        $isNever = null !== $maximum && $maximum < 1;

        if ($isCall) {
            if ($isNever) {
                $expected =
                    'Expected ' . $renderedSubject .
                    ' call #' . $subject->index() .
                    ' not to ' . $renderedCriteria;
            } else {
                $expected =
                    'Expected ' . $renderedSubject .
                    ' call #' . $subject->index() .
                    ' to ' . $renderedCriteria;
            }
        } else {
            if ($isNever) {
                $expected =
                    'Expected ' . $renderedSubject .
                    ' not to ' . $renderedCriteria;
            } elseif ($cardinality->isAlways()) {
                $expected =
                    'Expected all ' . $renderedSubject .
                    ' calls to ' . $renderedCriteria;
            } else {
                $expected =
                    'Expected ' . $renderedSubject .
                    ' to ' . $renderedCriteria;
            }
        }

        $totalCount = count($calls);
        $matchCount = 0;

        if ($totalCount) {
            $renderedCalls = array();

            foreach ($calls as $call) {
                if ($responseEvent = $call->responseEvent()) {
                    list($exception, $returnValue) = $call->response();
                }

                if ($responseEvent instanceof ThrewEvent) {
                    if ($type instanceof Matcher) {
                        $isMatch = $type->matches($exception);
                    } elseif (is_string($type)) {
                        $isMatch = is_a($exception, $type);
                    } else {
                        $isMatch = true;
                    }
                } else {
                    $isMatch = false;
                }

                if ($isMatch) {
                    ++$matchCount;
                }

                if ($isMatch xor $isNever) {
                    $renderedResult = $this->pass;
                } else {
                    $renderedResult = $this->fail;
                }

                $renderedArguments = array();

                foreach ($call->arguments()->all() as $argument) {
                    $renderedArguments[] =
                        $this->exporter->export($argument, 0);
                }

                if ($responseEvent instanceof ReturnedEvent) {
                    $renderedResponse =
                        'Returned ' . $this->exporter->export($returnValue);
                } elseif ($responseEvent instanceof ThrewEvent) {
                    $renderedException = $this->exporter->export($exception);

                    if (!$isMatch && $type instanceof EqualToMatcher) {
                        $renderedException = $this->differenceEngine
                            ->difference($renderedType, $renderedException);
                    }

                    $renderedResponse = 'Threw ' . $renderedException;
                } else {
                    $renderedResponse = 'Never responded';
                }

                $renderedCalls[] =
                    $renderedResult . ' Call #' . $call->index() .
                    ' - ' . $renderedCallee .
                    '(' . implode(', ', $renderedArguments) . '):' .
                    PHP_EOL . '    ' . $renderedResult .
                    ' ' . $renderedResponse;
            }

            $actual = PHP_EOL . implode(PHP_EOL, $renderedCalls);
        } else {
            $actual = '';
        }

        if ($isCall) {
            $cardinality = '';
        } else {
            $cardinality = $this->renderCardinality(
                $minimum,
                $maximum,
                $matchCount,
                $totalCount,
                $totalCount,
                false
            );
        }

        return $this->reset . $expected . $cardinality . $actual;
    }

    /**
     * Render a failed generated() verification.
     *
     * @param Spy|Call    $subject     The subject.
     * @param Cardinality $cardinality The cardinality.
     *
     * @return string The rendered failure message.
     */
    public function renderGenerated($subject, Cardinality $cardinality)
    {
        $isCall = $subject instanceof Call;

        if ($isCall) {
            $calls = array($subject);
            $renderedCallee =
                $this->exporter->exportCallable($subject->callback());
        } else {
            $calls = $subject->allCalls();
            $renderedCallee = $this->exporter->exportCallable($subject);
        }

        $renderedSubject = $this->bold . $renderedCallee . $this->reset;
        $renderedCriteria =
            'behave like:' . PHP_EOL .
            '    ' . $this->fail . ' Returned Generator';

        $minimum = $cardinality->minimum();
        $maximum = $cardinality->maximum();
        $isNever = null !== $maximum && $maximum < 1;

        if ($isCall) {
            if ($isNever) {
                $expected =
                    'Expected ' . $renderedSubject .
                    ' call #' . $subject->index() .
                    ' not to ' . $renderedCriteria;
            } else {
                $expected =
                    'Expected ' . $renderedSubject .
                    ' call #' . $subject->index() .
                    ' to ' . $renderedCriteria;
            }
        } else {
            if ($isNever) {
                $expected =
                    'Expected ' . $renderedSubject .
                    ' not to ' . $renderedCriteria;
            } elseif ($cardinality->isAlways()) {
                $expected =
                    'Expected all ' . $renderedSubject .
                    ' calls to ' . $renderedCriteria;
            } else {
                $expected =
                    'Expected ' . $renderedSubject .
                    ' to ' . $renderedCriteria;
            }
        }

        $totalCount = count($calls);
        $matchCount = 0;

        if ($totalCount) {
            $renderedCalls = array();

            foreach ($calls as $call) {
                if ($responseEvent = $call->responseEvent()) {
                    list($exception, $returnValue) = $call->response();
                }

                if ($responseEvent instanceof ReturnedEvent) {
                    $isMatch = $returnValue instanceof Generator;
                } else {
                    $isMatch = false;
                }

                if ($isMatch) {
                    ++$matchCount;
                }

                if ($isMatch xor $isNever) {
                    $renderedResult = $this->pass;
                } else {
                    $renderedResult = $this->fail;
                }

                $renderedArguments = array();

                foreach ($call->arguments()->all() as $argument) {
                    $renderedArguments[] =
                        $this->exporter->export($argument, 0);
                }

                if ($responseEvent instanceof ReturnedEvent) {
                    $renderedResponse =
                        'Returned ' . $this->exporter->export($returnValue);
                } elseif ($responseEvent instanceof ThrewEvent) {
                    $renderedResponse =
                        'Threw ' . $this->exporter->export($exception);
                } else {
                    $renderedResponse = 'Never responded';
                }

                $renderedCalls[] =
                    $renderedResult . ' Call #' . $call->index() .
                    ' - ' . $renderedCallee .
                    '(' . implode(', ', $renderedArguments) . '):' .
                    PHP_EOL . '    ' . $renderedResult .
                    ' ' . $renderedResponse;
            }

            $actual = PHP_EOL . implode(PHP_EOL, $renderedCalls);
        } else {
            $actual = '';
        }

        if ($isCall) {
            $cardinality = '';
        } else {
            $cardinality = $this->renderCardinality(
                $minimum,
                $maximum,
                $matchCount,
                $totalCount,
                $totalCount,
                false
            );
        }

        return $this->reset . $expected . $cardinality . $actual;
    }

    /**
     * Render a failed iterated() verification.
     *
     * @param Spy|Call    $subject     The subject.
     * @param Cardinality $cardinality The cardinality.
     *
     * @return string The rendered failure message.
     */
    public function renderIterated($subject, Cardinality $cardinality)
    {
        $isCall = $subject instanceof Call;

        if ($isCall) {
            $calls = array($subject);
            $renderedCallee =
                $this->exporter->exportCallable($subject->callback());
        } else {
            $calls = $subject->allCalls();
            $renderedCallee = $this->exporter->exportCallable($subject);
        }

        $renderedSubject = $this->bold . $renderedCallee . $this->reset;
        $renderedCriteria =
            'behave like:' . PHP_EOL .
            '    ' . $this->fail . ' Returned <iterable>';

        $minimum = $cardinality->minimum();
        $maximum = $cardinality->maximum();
        $isNever = null !== $maximum && $maximum < 1;

        if ($isCall) {
            if ($isNever) {
                $expected =
                    'Expected ' . $renderedSubject .
                    ' call #' . $subject->index() .
                    ' not to ' . $renderedCriteria;
            } else {
                $expected =
                    'Expected ' . $renderedSubject .
                    ' call #' . $subject->index() .
                    ' to ' . $renderedCriteria;
            }
        } else {
            if ($isNever) {
                $expected =
                    'Expected ' . $renderedSubject .
                    ' not to ' . $renderedCriteria;
            } elseif ($cardinality->isAlways()) {
                $expected =
                    'Expected all ' . $renderedSubject .
                    ' calls to ' . $renderedCriteria;
            } else {
                $expected =
                    'Expected ' . $renderedSubject .
                    ' to ' . $renderedCriteria;
            }
        }

        $totalCount = count($calls);
        $matchCount = 0;

        if ($totalCount) {
            $renderedCalls = array();

            foreach ($calls as $call) {
                if ($responseEvent = $call->responseEvent()) {
                    list($exception, $returnValue) = $call->response();
                }

                if ($responseEvent instanceof ReturnedEvent) {
                    $isMatch = $returnValue instanceof Traversable ||
                        is_array($returnValue);
                } else {
                    $isMatch = false;
                }

                if ($isMatch) {
                    ++$matchCount;
                }

                if ($isMatch xor $isNever) {
                    $renderedResult = $this->pass;
                } else {
                    $renderedResult = $this->fail;
                }

                $renderedArguments = array();

                foreach ($call->arguments()->all() as $argument) {
                    $renderedArguments[] =
                        $this->exporter->export($argument, 0);
                }

                if ($responseEvent instanceof ReturnedEvent) {
                    $renderedResponse =
                        'Returned ' . $this->exporter->export($returnValue);
                } elseif ($responseEvent instanceof ThrewEvent) {
                    $renderedResponse =
                        'Threw ' . $this->exporter->export($exception);
                } else {
                    $renderedResponse = 'Never responded';
                }

                $renderedCalls[] =
                    $renderedResult . ' Call #' . $call->index() .
                    ' - ' . $renderedCallee .
                    '(' . implode(', ', $renderedArguments) . '):' .
                    PHP_EOL . '    ' . $renderedResult .
                    ' ' . $renderedResponse;
            }

            $actual = PHP_EOL . implode(PHP_EOL, $renderedCalls);
        } else {
            $actual = '';
        }

        if ($isCall) {
            $cardinality = '';
        } else {
            $cardinality = $this->renderCardinality(
                $minimum,
                $maximum,
                $matchCount,
                $totalCount,
                $totalCount,
                false
            );
        }

        return $this->reset . $expected . $cardinality . $actual;
    }

    /**
     * Render a failed iterable used() verification.
     *
     * @param Spy|Call    $subject     The subject.
     * @param Cardinality $cardinality The cardinality.
     * @param bool        $isGenerator True if this verification is for a generator.
     *
     * @return string The rendered failure message.
     */
    public function renderIterableUsed(
        $subject,
        Cardinality $cardinality,
        $isGenerator
    ) {
        $isCall = $subject instanceof Call;

        if ($isCall) {
            $calls = array($subject);
            $renderedCallee =
                $this->exporter->exportCallable($subject->callback());
        } else {
            $calls = $subject->allCalls();
            $renderedCallee = $this->exporter->exportCallable($subject);
        }

        $renderedSubject = $this->bold . $renderedCallee . $this->reset;

        $minimum = $cardinality->minimum();
        $maximum = $cardinality->maximum();
        $isNever = null !== $maximum && $maximum < 1;

        if ($isCall) {
            $totalCount = 1;

            if ($isNever) {
                $iterableResult = $this->fail;
            } else {
                $iterableResult = $this->pass;
            }

            $renderedIterableCount = '';
        } else {
            $totalCount = 0;
            $iterableCount = 0;

            foreach ($calls as $call) {
                ++$totalCount;

                if ($isGenerator) {
                    $isIterable = $call->isGenerator();
                } else {
                    $isIterable = $call->isIterable();
                }

                if ($isIterable) {
                    ++$iterableCount;
                }
            }

            if ($cardinality->matches($iterableCount, $iterableCount)) {
                $iterableResultStart = $this->passStart;
                $iterableResultText = self::PASS;
            } else {
                $iterableResultStart = $this->failStart;
                $iterableResultText = self::FAIL;
            }

            $iterableResult =
                $iterableResultStart .
                $iterableResultText .
                $this->reset;
            $matchOrMatches = 1 === $iterableCount ? 'match' : 'matches';
            $renderedIterableCount =
                ' ' . $iterableResultStart . $this->faint .
                '(' . $iterableCount . ' ' . $matchOrMatches . ')' .
                $this->reset;
        }

        if ($isGenerator) {
            $renderedIterableType = 'Generator';
        } else {
            $renderedIterableType = '<iterable>';
        }

        $renderedCriteria =
            'behave like:' . PHP_EOL .
            '    ' . $iterableResult .
            ' Returned ' . $renderedIterableType .
            ', then:' . $renderedIterableCount . PHP_EOL .
            '        ' . $this->fail . ' Started iterating';

        if ($isCall) {
            if ($isNever) {
                $expected =
                    'Expected ' . $renderedSubject .
                    ' call #' . $subject->index() .
                    ' not to ' . $renderedCriteria;
            } else {
                $expected =
                    'Expected ' . $renderedSubject .
                    ' call #' . $subject->index() .
                    ' to ' . $renderedCriteria;
            }
        } else {
            if ($isGenerator) {
                $renderedIterableType = 'generator calls';
            } else {
                $renderedIterableType = 'iterable calls';
            }

            if ($isNever) {
                $expected =
                    'Expected ' . $renderedSubject .
                    ' ' . $renderedIterableType .
                    ' not to ' . $renderedCriteria;
            } elseif ($cardinality->isAlways()) {
                $expected =
                    'Expected all ' . $renderedSubject .
                    ' ' . $renderedIterableType .
                    ' to ' . $renderedCriteria;
            } else {
                $expected =
                    'Expected ' . $renderedSubject .
                    ' ' . $renderedIterableType .
                    ' to ' . $renderedCriteria;
            }
        }

        $renderedCalls = array();
        $matchCount = 0;

        foreach ($calls as $call) {
            if ($isGenerator) {
                $callIsRelevant = $call->isGenerator();
            } else {
                $callIsRelevant = $call->isIterable();
            }

            if ($callIsRelevant) {
                $callStart = '';
                $callEnd = '';
            } else {
                $callStart = $this->faint;
                $callEnd = $this->reset;
            }

            $isMatch = false;
            $renderedArguments = array();

            foreach ($call->arguments()->all() as $argument) {
                $renderedArguments[] = $this->exporter->export($argument, 0);
            }

            $responseEvent = $call->responseEvent();

            if ($responseEvent instanceof ReturnedEvent) {
                $returnValue = $responseEvent->value();

                if (
                    is_array($returnValue) ||
                    $returnValue instanceof Traversable
                ) {
                    $iterableEvents = $call->iterableEvents();
                    $renderedIterableEvents = array();

                    foreach ($iterableEvents as $event) {
                        if ($event instanceof UsedEvent) {
                            if ($callIsRelevant) {
                                $isMatch = true;

                                if ($isNever) {
                                    $eventResult = $this->fail;
                                } else {
                                    $eventResult = $this->pass;
                                }
                            } else {
                                $eventResult = '-';
                            }

                            $renderedIterableEvents[] =
                                '        ' . $eventResult .
                                ' Started iterating';
                        } elseif ($event instanceof ProducedEvent) {
                            $iterableKey = $event->key();
                            $iterableValue = $event->value();

                            $renderedIterableEvents[] =
                                '        - Produced ' .
                                $this->exporter->export($iterableKey) .
                                ' => ' .
                                $this->exporter->export($iterableValue);
                        } elseif ($event instanceof ReceivedEvent) {
                            $iterableValue = $event->value();

                            $renderedIterableEvents[] =
                                '        - Received ' .
                                $this->exporter->export($iterableValue);
                        } elseif (
                            $event instanceof ReceivedExceptionEvent
                        ) {
                            $iterableException = $event->exception();

                            $renderedIterableEvents[] =
                                '        - Received exception ' .
                                $this->exporter->export($iterableException);
                        }
                    }

                    $endEvent = $call->endEvent();

                    if (empty($iterableEvents)) {
                        if ($callIsRelevant) {
                            if ($isNever) {
                                $eventResult = $this->pass;
                            } else {
                                $eventResult = $this->fail;
                            }
                        } else {
                            $eventResult = '-';
                        }

                        $renderedIterableEvents[] =
                            '        ' . $eventResult .
                            ' Never started iterating';
                    } elseif ($endEvent instanceof ConsumedEvent) {
                        $renderedIterableEvents[] =
                            '        - Finished iterating';
                    } elseif ($endEvent instanceof ReturnedEvent) {
                        $eventValue = $endEvent->value();

                        $renderedIterableEvents[] =
                            '        - Returned ' .
                            $this->exporter->export($eventValue);
                    } elseif ($endEvent instanceof ThrewEvent) {
                        $eventException = $endEvent->exception();

                        $renderedIterableEvents[] =
                            '        - Threw ' .
                            $this->exporter->export($eventException);
                    } else {
                        $renderedIterableEvents[] =
                            '        - Never finished iterating';
                    }

                    $renderedResponse =
                        'Returned ' . $this->exporter->export($returnValue, 0) .
                        ', then:' . $callEnd . PHP_EOL . $callStart .
                        implode(
                            $callEnd . PHP_EOL . $callStart,
                            $renderedIterableEvents
                        );
                } else {
                    $renderedResponse =
                        'Returned ' . $this->exporter->export($returnValue);
                }
            } elseif ($responseEvent instanceof ThrewEvent) {
                $exception = $responseEvent->exception();
                $renderedResponse =
                    'Threw ' . $this->exporter->export($exception);
            } else {
                $renderedResponse = 'Never responded';
            }

            if ($isMatch) {
                ++$matchCount;
            }

            if ($callIsRelevant) {
                if ($isMatch xor $isNever) {
                    $renderedResult = $this->pass;
                } else {
                    $renderedResult = $this->fail;
                }
            } else {
                $renderedResult = '-';
            }

            $renderedCalls[] =
                $callStart . $renderedResult . ' Call #' . $call->index() .
                ' - ' . $renderedCallee .
                '(' . implode(', ', $renderedArguments) . '):' .
                $callEnd . PHP_EOL .
                $callStart . '    ' . $renderedResult . ' ' .
                $renderedResponse . $callEnd;
        }

        $actual = PHP_EOL . implode(PHP_EOL, $renderedCalls);

        if ($isCall) {
            $cardinality = '';
        } else {
            $cardinality = $this->renderCardinality(
                $minimum,
                $maximum,
                $matchCount,
                $iterableCount,
                $totalCount,
                false
            );
        }

        return $this->reset . $expected . $cardinality . $actual;
    }

    /**
     * Render a failed iterable produced() verification.
     *
     * @param Spy|Call     $subject     The subject.
     * @param Cardinality  $cardinality The cardinality.
     * @param bool         $isGenerator True if this verification is for a generator.
     * @param Matcher|null $key         The key.
     * @param Matcher|null $value       The value.
     *
     * @return string The rendered failure message.
     */
    public function renderIterableProduced(
        $subject,
        Cardinality $cardinality,
        $isGenerator,
        Matcher $key = null,
        Matcher $value = null
    ) {
        $isCall = $subject instanceof Call;

        if ($isCall) {
            $calls = array($subject);
            $renderedCallee =
                $this->exporter->exportCallable($subject->callback());
        } else {
            $calls = $subject->allCalls();
            $renderedCallee = $this->exporter->exportCallable($subject);
        }

        $renderedSubject = $this->bold . $renderedCallee . $this->reset;

        $minimum = $cardinality->minimum();
        $maximum = $cardinality->maximum();
        $isNever = null !== $maximum && $maximum < 1;

        if ($isCall) {
            $totalCount = 0;
            $callCount = 1;
            $iterableCount = 1;

            foreach ($subject->iterableEvents() as $event) {
                if ($event instanceof ProducedEvent) {
                    ++$totalCount;
                }
            }

            $renderedIterableCount = '';
        } else {
            $callCount = 0;
            $iterableCount = 0;

            foreach ($calls as $call) {
                ++$callCount;

                if ($isGenerator) {
                    $isIterable = $call->isGenerator();
                } else {
                    $isIterable = $call->isIterable();
                }

                if ($isIterable) {
                    ++$iterableCount;
                }
            }

            $totalCount = $iterableCount;

            $matchOrMatches = 1 === $iterableCount ? 'match' : 'matches';
            $renderedIterableCount =
                ' ' . $this->faint .
                '(' . $iterableCount .
                ' ' . $matchOrMatches .
                ')' . $this->reset;
        }

        if ($iterableCount xor $isNever) {
            $iterableResult = $this->pass;
        } else {
            $iterableResult = $this->fail;
        }

        if ($isGenerator) {
            $renderedIterableType = 'Generator';
        } else {
            $renderedIterableType = '<iterable>';
        }

        if ($key) {
            $renderedKey = $key->describe($this->exporter);
        } else {
            $renderedKey = '<any>';
        }

        if ($value) {
            $renderedValue = $value->describe($this->exporter);
        } else {
            $renderedValue = '<any>';
        }

        $renderedCriteria =
            'behave like:' . PHP_EOL .
            '    ' . $iterableResult .
            ' Returned ' . $renderedIterableType .
            ', then:' . $renderedIterableCount . PHP_EOL .
            '        ' . $this->fail .
            ' Produced ' . $renderedKey .
            ' => ' . $renderedValue;

        if ($isCall) {
            if ($isNever) {
                $expected =
                    'Expected ' . $renderedSubject .
                    ' call #' . $subject->index() .
                    ' not to ' . $renderedCriteria;
            } else {
                $expected =
                    'Expected ' . $renderedSubject .
                    ' call #' . $subject->index() .
                    ' to ' . $renderedCriteria;
            }
        } else {
            if ($isGenerator) {
                $renderedIterableType = 'generator calls';
            } else {
                $renderedIterableType = 'iterable calls';
            }

            if ($isNever) {
                $expected =
                    'Expected ' . $renderedSubject .
                    ' ' . $renderedIterableType .
                    ' not to ' . $renderedCriteria;
            } elseif ($cardinality->isAlways()) {
                $expected =
                    'Expected all ' . $renderedSubject .
                    ' ' . $renderedIterableType .
                    ' to ' . $renderedCriteria;
            } else {
                $expected =
                    'Expected ' . $renderedSubject .
                    ' ' . $renderedIterableType .
                    ' to ' . $renderedCriteria;
            }
        }

        $renderedCalls = array();
        $matchCount = 0;

        foreach ($calls as $call) {
            if ($isGenerator) {
                $callIsRelevant = $call->isGenerator();
            } else {
                $callIsRelevant = $call->isIterable();
            }

            if ($callIsRelevant) {
                $callStart = '';
                $callEnd = '';
            } else {
                $callStart = $this->faint;
                $callEnd = $this->reset;
            }

            $isMatchingCall = false;
            $renderedArguments = array();

            foreach ($call->arguments()->all() as $argument) {
                $renderedArguments[] = $this->exporter->export($argument, 0);
            }

            $responseEvent = $call->responseEvent();

            if ($responseEvent instanceof ReturnedEvent) {
                $returnValue = $responseEvent->value();

                if (
                    is_array($returnValue) ||
                    $returnValue instanceof Traversable
                ) {
                    $iterableEvents = $call->iterableEvents();
                    $renderedIterableEvents = array();

                    foreach ($iterableEvents as $event) {
                        if ($event instanceof UsedEvent) {
                            $renderedIterableEvents[] =
                                '        - Started iterating';
                        } elseif ($event instanceof ProducedEvent) {
                            $iterableKey = $event->key();
                            $renderedIterableKey =
                                $this->exporter->export($iterableKey);

                            $iterableValue = $event->value();
                            $renderedIterableValue =
                                $this->exporter->export($iterableValue);

                            if ($callIsRelevant) {
                                $isKeyMatch =
                                    !$key || $key->matches($iterableKey);
                                $isValueMatch =
                                    !$value ||
                                    $value->matches($iterableValue);
                                $eventIsMatch = $isKeyMatch && $isValueMatch;

                                if ($eventIsMatch) {
                                    $isMatchingCall = true;

                                    if ($isCall) {
                                        ++$matchCount;
                                    }
                                }

                                if (
                                    !$isKeyMatch &&
                                    $key instanceof EqualToMatcher
                                ) {
                                    $renderedIterableKey =
                                        $this->differenceEngine->difference(
                                            $renderedKey,
                                            $renderedIterableKey
                                        );
                                }

                                if (
                                    !$isValueMatch &&
                                    $value instanceof EqualToMatcher
                                ) {
                                    $renderedIterableValue =
                                        $this->differenceEngine->difference(
                                            $renderedValue,
                                            $renderedIterableValue
                                        );
                                }

                                if ($eventIsMatch xor $isNever) {
                                    $eventResult = $this->pass;
                                } else {
                                    $eventResult = $this->fail;
                                }
                            } else {
                                $eventResult = '-';
                            }

                            $renderedIterableEvents[] =
                                '        ' . $eventResult .
                                ' Produced ' . $renderedIterableKey .
                                ' => ' . $renderedIterableValue;
                        } elseif ($event instanceof ReceivedEvent) {
                            $iterableValue = $event->value();

                            $renderedIterableEvents[] =
                                '        - Received ' .
                                $this->exporter->export($iterableValue);
                        } elseif ($event instanceof ReceivedExceptionEvent) {
                            $iterableException = $event->exception();

                            $renderedIterableEvents[] =
                                '        - Received exception ' .
                                $this->exporter->export($iterableException);
                        }
                    }

                    $endEvent = $call->endEvent();

                    if (empty($iterableEvents)) {
                        if ($callIsRelevant) {
                            if ($isNever) {
                                $eventResult = $this->pass;
                            } else {
                                $eventResult = $this->fail;
                            }
                        } else {
                            $eventResult = '-';
                        }

                        $renderedIterableEvents[] =
                            '        ' . $eventResult .
                            ' Never started iterating';
                    } elseif ($endEvent instanceof ConsumedEvent) {
                        $renderedIterableEvents[] =
                            '        - Finished iterating';
                    } elseif ($endEvent instanceof ReturnedEvent) {
                        $eventValue = $endEvent->value();

                        $renderedIterableEvents[] =
                            '        - Returned ' .
                            $this->exporter->export($eventValue);
                    } elseif ($endEvent instanceof ThrewEvent) {
                        $eventException = $endEvent->exception();

                        $renderedIterableEvents[] =
                            '        - Threw ' .
                            $this->exporter->export($eventException);
                    } else {
                        $renderedIterableEvents[] =
                            '        - Never finished iterating';
                    }

                    $renderedResponse =
                        'Returned ' . $this->exporter->export($returnValue, 0) .
                        ', then:' . $callEnd . PHP_EOL . $callStart .
                        implode(
                            $callEnd . PHP_EOL . $callStart,
                            $renderedIterableEvents
                        );
                } else {
                    $renderedResponse =
                        'Returned ' . $this->exporter->export($returnValue);
                }
            } elseif ($responseEvent instanceof ThrewEvent) {
                $exception = $responseEvent->exception();
                $renderedResponse =
                    'Threw ' . $this->exporter->export($exception);
            } else {
                $renderedResponse = 'Never responded';
            }

            if (!$isCall && $isMatchingCall) {
                ++$matchCount;
            }

            if ($callIsRelevant) {
                if ($isMatchingCall xor $isNever) {
                    $renderedResult = $this->pass;
                } else {
                    $renderedResult = $this->fail;
                }
            } else {
                $renderedResult = '-';
            }

            $renderedCalls[] =
                $callStart . $renderedResult . ' Call #' . $call->index() .
                ' - ' . $renderedCallee .
                '(' . implode(', ', $renderedArguments) . '):' .
                $callEnd . PHP_EOL .
                $callStart . '    ' . $renderedResult . ' ' .
                $renderedResponse . $callEnd;
        }

        $actual = PHP_EOL . implode(PHP_EOL, $renderedCalls);
        $cardinality = $this->renderCardinality(
            $minimum,
            $maximum,
            $matchCount,
            $totalCount,
            $callCount,
            false
        );

        return $this->reset . $expected . $cardinality . $actual;
    }

    /**
     * Render a failed iterable consumed() verification.
     *
     * @param Spy|Call    $subject     The subject.
     * @param Cardinality $cardinality The cardinality.
     * @param bool        $isGenerator True if this verification is for a generator.
     *
     * @return string The rendered failure message.
     */
    public function renderIterableConsumed(
        $subject,
        Cardinality $cardinality,
        $isGenerator
    ) {
        $isCall = $subject instanceof Call;

        if ($isCall) {
            $calls = array($subject);
            $renderedCallee =
                $this->exporter->exportCallable($subject->callback());
        } else {
            $calls = $subject->allCalls();
            $renderedCallee = $this->exporter->exportCallable($subject);
        }

        $renderedSubject = $this->bold . $renderedCallee . $this->reset;

        $minimum = $cardinality->minimum();
        $maximum = $cardinality->maximum();
        $isNever = null !== $maximum && $maximum < 1;

        if ($isCall) {
            $totalCount = 1;
            $iterableCount = 1;

            if ($isNever) {
                $iterableResult = $this->fail;
            } else {
                $iterableResult = $this->pass;
            }

            $renderedIterableCount = '';
        } else {
            $totalCount = 0;
            $iterableCount = 0;

            foreach ($calls as $call) {
                ++$totalCount;

                if ($isGenerator) {
                    $isIterable = $call->isGenerator();
                } else {
                    $isIterable = $call->isIterable();
                }

                if ($isIterable) {
                    ++$iterableCount;
                }
            }

            if ($cardinality->matches($iterableCount, $iterableCount)) {
                $iterableResultStart = $this->passStart;
                $iterableResultText = self::PASS;
            } else {
                $iterableResultStart = $this->failStart;
                $iterableResultText = self::FAIL;
            }

            $iterableResult =
                $iterableResultStart .
                $iterableResultText .
                $this->reset;
            $matchOrMatches = 1 === $iterableCount ? 'match' : 'matches';
            $renderedIterableCount =
                ' ' . $iterableResultStart . $this->faint .
                '(' . $iterableCount . ' ' . $matchOrMatches . ')' .
                $this->reset;
        }

        if ($isGenerator) {
            $renderedIterableType = 'Generator';
        } else {
            $renderedIterableType = '<iterable>';
        }

        $renderedCriteria =
            'behave like:' . PHP_EOL .
            '    ' . $iterableResult .
            ' Returned ' . $renderedIterableType .
            ', then:' . $renderedIterableCount . PHP_EOL .
            '        ' . $this->fail . ' Finished iterating';

        if ($isCall) {
            if ($isNever) {
                $expected =
                    'Expected ' . $renderedSubject .
                    ' call #' . $subject->index() .
                    ' not to ' . $renderedCriteria;
            } else {
                $expected =
                    'Expected ' . $renderedSubject .
                    ' call #' . $subject->index() .
                    ' to ' . $renderedCriteria;
            }
        } else {
            if ($isGenerator) {
                $renderedIterableType = 'generator calls';
            } else {
                $renderedIterableType = 'iterable calls';
            }

            if ($isNever) {
                $expected =
                    'Expected ' . $renderedSubject .
                    ' ' . $renderedIterableType .
                    ' not to ' . $renderedCriteria;
            } elseif ($cardinality->isAlways()) {
                $expected =
                    'Expected all ' . $renderedSubject .
                    ' ' . $renderedIterableType .
                    ' to ' . $renderedCriteria;
            } else {
                $expected =
                    'Expected ' . $renderedSubject .
                    ' ' . $renderedIterableType .
                    ' to ' . $renderedCriteria;
            }
        }

        $renderedCalls = array();
        $matchCount = 0;

        foreach ($calls as $call) {
            if ($isGenerator) {
                $callIsRelevant = $call->isGenerator();
            } else {
                $callIsRelevant = $call->isIterable();
            }

            if ($callIsRelevant) {
                $callStart = '';
                $callEnd = '';
            } else {
                $callStart = $this->faint;
                $callEnd = $this->reset;
            }

            $isMatch = false;
            $renderedArguments = array();

            foreach ($call->arguments()->all() as $argument) {
                $renderedArguments[] = $this->exporter->export($argument, 0);
            }

            $responseEvent = $call->responseEvent();

            if ($responseEvent instanceof ReturnedEvent) {
                $returnValue = $responseEvent->value();

                if (
                    is_array($returnValue) ||
                    $returnValue instanceof Traversable
                ) {
                    $iterableEvents = $call->iterableEvents();
                    $renderedIterableEvents = array();

                    foreach ($iterableEvents as $event) {
                        if ($event instanceof UsedEvent) {
                            $renderedIterableEvents[] =
                                '        - Started iterating';
                        } elseif ($event instanceof ProducedEvent) {
                            $iterableKey = $event->key();
                            $iterableValue = $event->value();

                            $renderedIterableEvents[] =
                                '        - Produced ' .
                                $this->exporter->export($iterableKey) .
                                ' => ' .
                                $this->exporter->export($iterableValue);
                        } elseif ($event instanceof ReceivedEvent) {
                            $iterableValue = $event->value();

                            $renderedIterableEvents[] =
                                '        - Received ' .
                                $this->exporter->export($iterableValue);
                        } elseif (
                            $event instanceof ReceivedExceptionEvent
                        ) {
                            $iterableException = $event->exception();

                            $renderedIterableEvents[] =
                                '        - Received exception ' .
                                $this->exporter->export($iterableException);
                        }
                    }

                    $endEvent = $call->endEvent();

                    if (empty($iterableEvents)) {
                        if ($callIsRelevant) {
                            if ($isNever) {
                                $eventResult = $this->pass;
                            } else {
                                $eventResult = $this->fail;
                            }
                        } else {
                            $eventResult = '-';
                        }

                        $renderedIterableEvents[] =
                            '        ' . $eventResult .
                            ' Never started iterating';
                    } elseif ($endEvent instanceof ConsumedEvent) {
                        if ($callIsRelevant) {
                            $isMatch = true;

                            if ($isNever) {
                                $eventResult = $this->fail;
                            } else {
                                $eventResult = $this->pass;
                            }
                        } else {
                            $eventResult = '-';
                        }

                        $renderedIterableEvents[] =
                            '        ' . $eventResult . ' Finished iterating';
                    } elseif ($endEvent instanceof ReturnedEvent) {
                        $isMatch = true;
                        $iterableValue = $endEvent->value();

                        if ($isNever) {
                            $eventResult = $this->fail;
                        } else {
                            $eventResult = $this->pass;
                        }

                        $renderedIterableEvents[] =
                            '        ' . $eventResult . ' Returned ' .
                            $this->exporter->export($iterableValue);
                    } elseif ($endEvent instanceof ThrewEvent) {
                        $isMatch = true;

                        if ($isNever) {
                            $eventResult = $this->fail;
                        } else {
                            $eventResult = $this->pass;
                        }

                        $eventException = $endEvent->exception();

                        $renderedIterableEvents[] =
                            '        ' . $eventResult . ' Threw ' .
                            $this->exporter->export($eventException);
                    } else {
                        if ($callIsRelevant) {
                            if ($isNever) {
                                $eventResult = $this->pass;
                            } else {
                                $eventResult = $this->fail;
                            }
                        } else {
                            $eventResult = '-';
                        }

                        $renderedIterableEvents[] =
                            '        ' . $eventResult .
                            ' Never finished iterating';
                    }

                    $renderedResponse =
                        'Returned ' . $this->exporter->export($returnValue, 0) .
                        ', then:' . $callEnd . PHP_EOL . $callStart .
                        implode(
                            $callEnd . PHP_EOL . $callStart,
                            $renderedIterableEvents
                        );
                } else {
                    $renderedResponse =
                        'Returned ' . $this->exporter->export($returnValue);
                }
            } elseif ($responseEvent instanceof ThrewEvent) {
                $exception = $responseEvent->exception();
                $renderedResponse =
                    'Threw ' . $this->exporter->export($exception);
            } else {
                $renderedResponse = 'Never responded';
            }

            if ($isMatch) {
                ++$matchCount;
            }

            if ($callIsRelevant) {
                if ($isMatch xor $isNever) {
                    $renderedResult = $this->pass;
                } else {
                    $renderedResult = $this->fail;
                }
            } else {
                $renderedResult = '-';
            }

            $renderedCalls[] =
                $callStart . $renderedResult . ' Call #' . $call->index() .
                ' - ' . $renderedCallee .
                '(' . implode(', ', $renderedArguments) . '):' .
                $callEnd . PHP_EOL .
                $callStart . '    ' . $renderedResult . ' ' .
                $renderedResponse . $callEnd;
        }

        $actual = PHP_EOL . implode(PHP_EOL, $renderedCalls);

        if ($isCall) {
            $cardinality = '';
        } else {
            $cardinality = $this->renderCardinality(
                $minimum,
                $maximum,
                $matchCount,
                $iterableCount,
                $totalCount,
                false
            );
        }

        return $this->reset . $expected . $cardinality . $actual;
    }

    /**
     * Render a failed generator received() verification.
     *
     * @param Spy|Call     $subject     The subject.
     * @param Cardinality  $cardinality The cardinality.
     * @param Matcher|null $value       The value.
     *
     * @return string The rendered failure message.
     */
    public function renderGeneratorReceived(
        $subject,
        Cardinality $cardinality,
        Matcher $value = null
    ) {
        $isCall = $subject instanceof Call;

        if ($isCall) {
            $calls = array($subject);
            $renderedCallee =
                $this->exporter->exportCallable($subject->callback());
        } else {
            $calls = $subject->allCalls();
            $renderedCallee = $this->exporter->exportCallable($subject);
        }

        $renderedSubject = $this->bold . $renderedCallee . $this->reset;

        $minimum = $cardinality->minimum();
        $maximum = $cardinality->maximum();
        $isNever = null !== $maximum && $maximum < 1;

        if ($isCall) {
            $totalCount = 0;
            $callCount = 1;
            $iterableCount = 1;

            foreach ($subject->iterableEvents() as $event) {
                if ($event instanceof ReceivedEvent) {
                    ++$totalCount;
                }
            }

            $renderedIterableCount = '';
        } else {
            $callCount = 0;
            $iterableCount = 0;

            foreach ($calls as $call) {
                ++$callCount;

                if ($call->isGenerator()) {
                    ++$iterableCount;
                }
            }

            $totalCount = $iterableCount;

            $matchOrMatches = 1 === $iterableCount ? 'match' : 'matches';
            $renderedIterableCount =
                ' ' . $this->faint .
                '(' . $iterableCount .
                ' ' . $matchOrMatches .
                ')' . $this->reset;
        }

        if ($iterableCount xor $isNever) {
            $iterableResult = $this->pass;
        } else {
            $iterableResult = $this->fail;
        }

        if ($value) {
            $renderedValue = $value->describe($this->exporter);
        } else {
            $renderedValue = '<any>';
        }

        $renderedCriteria =
            'behave like:' . PHP_EOL . '    ' . $iterableResult .
            ' Returned Generator, then:' . $renderedIterableCount . PHP_EOL .
            '        ' . $this->fail . ' Received ' . $renderedValue;

        if ($isCall) {
            if ($isNever) {
                $expected =
                    'Expected ' . $renderedSubject .
                    ' call #' . $subject->index() .
                    ' not to ' . $renderedCriteria;
            } else {
                $expected =
                    'Expected ' . $renderedSubject .
                    ' call #' . $subject->index() .
                    ' to ' . $renderedCriteria;
            }
        } else {
            if ($isNever) {
                $expected =
                    'Expected ' . $renderedSubject .
                    ' generator calls not to ' . $renderedCriteria;
            } elseif ($cardinality->isAlways()) {
                $expected =
                    'Expected all ' . $renderedSubject .
                    ' generator calls to ' . $renderedCriteria;
            } else {
                $expected =
                    'Expected ' . $renderedSubject .
                    ' generator calls to ' . $renderedCriteria;
            }
        }

        $renderedCalls = array();
        $matchCount = 0;

        foreach ($calls as $call) {
            $callIsRelevant = $call->isGenerator();

            if ($callIsRelevant) {
                $callStart = '';
                $callEnd = '';
            } else {
                $callStart = $this->faint;
                $callEnd = $this->reset;
            }

            $isMatchingCall = false;
            $renderedArguments = array();

            foreach ($call->arguments()->all() as $argument) {
                $renderedArguments[] = $this->exporter->export($argument, 0);
            }

            $responseEvent = $call->responseEvent();

            if ($responseEvent instanceof ReturnedEvent) {
                $returnValue = $responseEvent->value();

                if (
                    is_array($returnValue) ||
                    $returnValue instanceof Traversable
                ) {
                    $iterableEvents = $call->iterableEvents();
                    $renderedIterableEvents = array();

                    foreach ($iterableEvents as $event) {
                        if ($event instanceof UsedEvent) {
                            $renderedIterableEvents[] =
                                '        - Started iterating';
                        } elseif ($event instanceof ProducedEvent) {
                            $iterableKey = $event->key();
                            $iterableValue = $event->value();

                            $renderedIterableEvents[] =
                                '        - Produced ' .
                                $this->exporter->export($iterableKey) .
                                ' => ' .
                                $this->exporter->export($iterableValue);
                        } elseif ($event instanceof ReceivedEvent) {
                            $iterableValue = $event->value();
                            $renderedIterableValue =
                                $this->exporter->export($iterableValue);

                            $eventIsMatch =
                                !$value ||
                                $value->matches($iterableValue);

                            if ($eventIsMatch) {
                                $isMatchingCall = true;

                                if ($isCall) {
                                    ++$matchCount;
                                }
                            } elseif (
                                $value instanceof EqualToMatcher
                            ) {
                                $renderedIterableValue =
                                    $this->differenceEngine->difference(
                                        $renderedValue,
                                        $renderedIterableValue
                                    );
                            }

                            if ($eventIsMatch xor $isNever) {
                                $eventResult = $this->pass;
                            } else {
                                $eventResult = $this->fail;
                            }

                            $renderedIterableEvents[] =
                                '        ' . $eventResult . ' Received ' .
                                $renderedIterableValue;
                        } elseif (
                            $event instanceof ReceivedExceptionEvent
                        ) {
                            $iterableException = $event->exception();

                            $renderedIterableEvents[] =
                                '        - Received exception ' .
                                $this->exporter->export($iterableException);
                        }
                    }

                    $endEvent = $call->endEvent();

                    if (empty($iterableEvents)) {
                        if ($callIsRelevant) {
                            if ($isNever) {
                                $eventResult = $this->pass;
                            } else {
                                $eventResult = $this->fail;
                            }
                        } else {
                            $eventResult = '-';
                        }

                        $renderedIterableEvents[] =
                            '        ' . $eventResult .
                            ' Never started iterating';
                    } elseif ($endEvent instanceof ConsumedEvent) {
                        $renderedIterableEvents[] =
                            '        - Finished iterating';
                    } elseif ($endEvent instanceof ReturnedEvent) {
                        $eventValue = $endEvent->value();

                        $renderedIterableEvents[] =
                            '        - Returned ' .
                            $this->exporter->export($eventValue);
                    } elseif ($endEvent instanceof ThrewEvent) {
                        $eventException = $endEvent->exception();

                        $renderedIterableEvents[] =
                            '        - Threw ' .
                            $this->exporter->export($eventException);
                    } else {
                        $renderedIterableEvents[] =
                            '        - Never finished iterating';
                    }

                    $renderedResponse =
                        'Returned ' . $this->exporter->export($returnValue, 0) .
                        ', then:' . $callEnd . PHP_EOL . $callStart .
                        implode(
                            $callEnd . PHP_EOL . $callStart,
                            $renderedIterableEvents
                        );
                } else {
                    $renderedResponse =
                        'Returned ' . $this->exporter->export($returnValue);
                }
            } elseif ($responseEvent instanceof ThrewEvent) {
                $exception = $responseEvent->exception();
                $renderedResponse =
                    'Threw ' . $this->exporter->export($exception);
            } else {
                $renderedResponse = 'Never responded';
            }

            if (!$isCall && $isMatchingCall) {
                ++$matchCount;
            }

            if ($callIsRelevant) {
                if ($isMatchingCall xor $isNever) {
                    $renderedResult = $this->pass;
                } else {
                    $renderedResult = $this->fail;
                }
            } else {
                $renderedResult = '-';
            }

            $renderedCalls[] =
                $callStart . $renderedResult . ' Call #' . $call->index() .
                ' - ' . $renderedCallee .
                '(' . implode(', ', $renderedArguments) . '):' .
                $callEnd . PHP_EOL .
                $callStart . '    ' . $renderedResult . ' ' .
                $renderedResponse . $callEnd;
        }

        $actual = PHP_EOL . implode(PHP_EOL, $renderedCalls);
        $cardinality = $this->renderCardinality(
            $minimum,
            $maximum,
            $matchCount,
            $totalCount,
            $callCount,
            false
        );

        return $this->reset . $expected . $cardinality . $actual;
    }

    /**
     * Render a failed generator receivedException() verification.
     *
     * @param Spy|Call                            $subject     The subject.
     * @param Cardinality                         $cardinality The cardinality.
     * @param Matcher|Exception|Error|string|null $type        The type of exception.
     *
     * @return string The rendered failure message.
     */
    public function renderGeneratorReceivedException(
        $subject,
        Cardinality $cardinality,
        $type
    ) {
        $isCall = $subject instanceof Call;

        if ($isCall) {
            $calls = array($subject);
            $renderedCallee =
                $this->exporter->exportCallable($subject->callback());
        } else {
            $calls = $subject->allCalls();
            $renderedCallee = $this->exporter->exportCallable($subject);
        }

        $renderedSubject = $this->bold . $renderedCallee . $this->reset;

        $minimum = $cardinality->minimum();
        $maximum = $cardinality->maximum();
        $isNever = null !== $maximum && $maximum < 1;

        if ($isCall) {
            $totalCount = 0;
            $callCount = 1;
            $iterableCount = 1;

            foreach ($subject->iterableEvents() as $event) {
                if ($event instanceof ReceivedExceptionEvent) {
                    ++$totalCount;
                }
            }

            $renderedIterableCount = '';
        } else {
            $callCount = 0;
            $iterableCount = 0;

            foreach ($calls as $call) {
                ++$callCount;

                if ($call->isGenerator()) {
                    ++$iterableCount;
                }
            }

            $totalCount = $iterableCount;

            $matchOrMatches = 1 === $iterableCount ? 'match' : 'matches';
            $renderedIterableCount =
                ' ' . $this->faint .
                '(' . $iterableCount .
                ' ' . $matchOrMatches .
                ')' . $this->reset;
        }

        if ($iterableCount xor $isNever) {
            $iterableResult = $this->pass;
        } else {
            $iterableResult = $this->fail;
        }

        if ($type instanceof Matcher) {
            $renderedType = $type->describe($this->exporter);
        } elseif (is_string($type)) {
            $renderedType = $type;
        } else {
            $renderedType = '<any>';
        }

        $renderedCriteria =
            'behave like:' . PHP_EOL . '    ' . $iterableResult .
            ' Returned Generator, then:' . $renderedIterableCount . PHP_EOL .
            '        ' . $this->fail . ' Received exception ' . $renderedType;

        if ($isCall) {
            if ($isNever) {
                $expected =
                    'Expected ' . $renderedSubject .
                    ' call #' . $subject->index() .
                    ' not to ' . $renderedCriteria;
            } else {
                $expected =
                    'Expected ' . $renderedSubject .
                    ' call #' . $subject->index() .
                    ' to ' . $renderedCriteria;
            }
        } else {
            if ($isNever) {
                $expected =
                    'Expected ' . $renderedSubject .
                    ' generator calls not to ' . $renderedCriteria;
            } elseif ($cardinality->isAlways()) {
                $expected =
                    'Expected all ' . $renderedSubject .
                    ' generator calls to ' . $renderedCriteria;
            } else {
                $expected =
                    'Expected ' . $renderedSubject .
                    ' generator calls to ' . $renderedCriteria;
            }
        }

        $renderedCalls = array();
        $matchCount = 0;

        foreach ($calls as $call) {
            $callIsRelevant = $call->isGenerator();

            if ($callIsRelevant) {
                $callStart = '';
                $callEnd = '';
            } else {
                $callStart = $this->faint;
                $callEnd = $this->reset;
            }

            $isMatchingCall = false;
            $renderedArguments = array();

            foreach ($call->arguments()->all() as $argument) {
                $renderedArguments[] = $this->exporter->export($argument, 0);
            }

            $responseEvent = $call->responseEvent();

            if ($responseEvent instanceof ReturnedEvent) {
                $returnValue = $responseEvent->value();

                if (
                    is_array($returnValue) ||
                    $returnValue instanceof Traversable
                ) {
                    $iterableEvents = $call->iterableEvents();
                    $renderedIterableEvents = array();

                    foreach ($iterableEvents as $event) {
                        if ($event instanceof UsedEvent) {
                            $renderedIterableEvents[] =
                                '        - Started iterating';
                        } elseif ($event instanceof ProducedEvent) {
                            $iterableKey = $event->key();
                            $iterableValue = $event->value();

                            $renderedIterableEvents[] =
                                '        - Produced ' .
                                $this->exporter->export($iterableKey) .
                                ' => ' .
                                $this->exporter->export($iterableValue);
                        } elseif ($event instanceof ReceivedEvent) {
                            $iterableValue = $event->value();

                            $renderedIterableEvents[] =
                                '        - Received ' .
                                $this->exporter->export($iterableValue);
                        } elseif (
                            $event instanceof ReceivedExceptionEvent
                        ) {
                            $iterableException = $event->exception();
                            $renderedIterableException =
                                $this->exporter->export($iterableException);

                            if ($type instanceof Matcher) {
                                $eventIsMatch =
                                    $type->matches($iterableException);
                            } elseif (is_string($type)) {
                                $eventIsMatch =
                                    is_a($iterableException, $type);
                            } else {
                                $eventIsMatch = true;
                            }

                            if ($eventIsMatch) {
                                $isMatchingCall = true;

                                if ($isCall) {
                                    ++$matchCount;
                                }
                            } elseif ($type instanceof EqualToMatcher) {
                                $renderedIterableException =
                                    $this->differenceEngine->difference(
                                        $renderedType,
                                        $renderedIterableException
                                    );
                            }

                            if ($eventIsMatch xor $isNever) {
                                $eventResult = $this->pass;
                            } else {
                                $eventResult = $this->fail;
                            }

                            $renderedIterableEvents[] =
                                '        ' . $eventResult .
                                ' Received exception ' .
                                $renderedIterableException;
                        }
                    }

                    $endEvent = $call->endEvent();

                    if (empty($iterableEvents)) {
                        if ($callIsRelevant) {
                            if ($isNever) {
                                $eventResult = $this->pass;
                            } else {
                                $eventResult = $this->fail;
                            }
                        } else {
                            $eventResult = '-';
                        }

                        $renderedIterableEvents[] =
                            '        ' . $eventResult .
                            ' Never started iterating';
                    } elseif ($endEvent instanceof ConsumedEvent) {
                        $renderedIterableEvents[] =
                            '        - Finished iterating';
                    } elseif ($endEvent instanceof ReturnedEvent) {
                        $eventValue = $endEvent->value();

                        $renderedIterableEvents[] =
                            '        - Returned ' .
                            $this->exporter->export($eventValue);
                    } elseif ($endEvent instanceof ThrewEvent) {
                        $eventException = $endEvent->exception();

                        $renderedIterableEvents[] =
                            '        - Threw ' .
                            $this->exporter->export($eventException);
                    } else {
                        $renderedIterableEvents[] =
                            '        - Never finished iterating';
                    }

                    $renderedResponse =
                        'Returned ' . $this->exporter->export($returnValue, 0) .
                        ', then:' . $callEnd . PHP_EOL . $callStart .
                        implode(
                            $callEnd . PHP_EOL . $callStart,
                            $renderedIterableEvents
                        );
                } else {
                    $renderedResponse =
                        'Returned ' . $this->exporter->export($returnValue);
                }
            } elseif ($responseEvent instanceof ThrewEvent) {
                $exception = $responseEvent->exception();
                $renderedResponse =
                    'Threw ' . $this->exporter->export($exception);
            } else {
                $renderedResponse = 'Never responded';
            }

            if (!$isCall && $isMatchingCall) {
                ++$matchCount;
            }

            if ($callIsRelevant) {
                if ($isMatchingCall xor $isNever) {
                    $renderedResult = $this->pass;
                } else {
                    $renderedResult = $this->fail;
                }
            } else {
                $renderedResult = '-';
            }

            $renderedCalls[] =
                $callStart . $renderedResult . ' Call #' . $call->index() .
                ' - ' . $renderedCallee .
                '(' . implode(', ', $renderedArguments) . '):' .
                $callEnd . PHP_EOL .
                $callStart . '    ' . $renderedResult . ' ' .
                $renderedResponse . $callEnd;
        }

        $actual = PHP_EOL . implode(PHP_EOL, $renderedCalls);
        $cardinality = $this->renderCardinality(
            $minimum,
            $maximum,
            $matchCount,
            $totalCount,
            $callCount,
            false
        );

        return $this->reset . $expected . $cardinality . $actual;
    }

    /**
     * Render a failed generator returned() verification.
     *
     * @param Spy|Call     $subject     The subject.
     * @param Cardinality  $cardinality The cardinality.
     * @param Matcher|null $value       The value.
     *
     * @return string The rendered failure message.
     */
    public function renderGeneratorReturned(
        $subject,
        Cardinality $cardinality,
        Matcher $value = null
    ) {
        $isCall = $subject instanceof Call;

        if ($isCall) {
            $calls = array($subject);
            $renderedCallee =
                $this->exporter->exportCallable($subject->callback());
        } else {
            $calls = $subject->allCalls();
            $renderedCallee = $this->exporter->exportCallable($subject);
        }

        $renderedSubject = $this->bold . $renderedCallee . $this->reset;

        $minimum = $cardinality->minimum();
        $maximum = $cardinality->maximum();
        $isNever = null !== $maximum && $maximum < 1;

        if ($isCall) {
            $totalCount = 1;
            $iterableCount = 1;
            $renderedIterableCount = '';
        } else {
            $iterableCount = 0;

            foreach ($calls as $call) {
                if ($call->isGenerator()) {
                    ++$iterableCount;
                }
            }

            $totalCount = $iterableCount;

            if ($cardinality->matches($iterableCount, $iterableCount)) {
                $iterableResultStart = $this->passStart;
            } else {
                $iterableResultStart = $this->failStart;
            }

            $matchOrMatches = 1 === $iterableCount ? 'match' : 'matches';
            $renderedIterableCount =
                ' ' . $iterableResultStart . $this->faint .
                '(' . $iterableCount . ' ' . $matchOrMatches . ')' .
                $this->reset;
        }

        if ($iterableCount xor $isNever) {
            $iterableResult = $this->pass;
        } else {
            $iterableResult = $this->fail;
        }

        if ($value) {
            $renderedValue = $value->describe($this->exporter);
        } else {
            $renderedValue = '<any>';
        }

        $renderedCriteria =
            'behave like:' . PHP_EOL . '    ' . $iterableResult .
            ' Returned Generator, then:' . $renderedIterableCount . PHP_EOL .
            '        ' . $this->fail . ' Returned ' . $renderedValue;

        if ($isCall) {
            if ($isNever) {
                $expected =
                    'Expected ' . $renderedSubject .
                    ' call #' . $subject->index() .
                    ' not to ' . $renderedCriteria;
            } else {
                $expected =
                    'Expected ' . $renderedSubject .
                    ' call #' . $subject->index() .
                    ' to ' . $renderedCriteria;
            }
        } else {
            if ($isNever) {
                $expected =
                    'Expected ' . $renderedSubject .
                    ' generator calls not to ' . $renderedCriteria;
            } elseif ($cardinality->isAlways()) {
                $expected =
                    'Expected all ' . $renderedSubject .
                    ' generator calls to ' . $renderedCriteria;
            } else {
                $expected =
                    'Expected ' . $renderedSubject .
                    ' generator calls to ' . $renderedCriteria;
            }
        }

        $renderedCalls = array();
        $matchCount = 0;

        foreach ($calls as $call) {
            $callIsRelevant = $call->isGenerator();

            if ($callIsRelevant) {
                $callStart = '';
                $callEnd = '';
            } else {
                $callStart = $this->faint;
                $callEnd = $this->reset;
            }

            $isMatchingCall = false;
            $renderedArguments = array();

            foreach ($call->arguments()->all() as $argument) {
                $renderedArguments[] = $this->exporter->export($argument, 0);
            }

            $responseEvent = $call->responseEvent();

            if ($responseEvent instanceof ReturnedEvent) {
                $returnValue = $responseEvent->value();

                if (
                    is_array($returnValue) ||
                    $returnValue instanceof Traversable
                ) {
                    $iterableEvents = $call->iterableEvents();
                    $renderedIterableEvents = array();

                    foreach ($iterableEvents as $event) {
                        if ($event instanceof UsedEvent) {
                            $renderedIterableEvents[] =
                                '        - Started iterating';
                        } elseif ($event instanceof ProducedEvent) {
                            $iterableKey = $event->key();
                            $iterableValue = $event->value();

                            $renderedIterableEvents[] =
                                '        - Produced ' .
                                $this->exporter->export($iterableKey) .
                                ' => ' .
                                $this->exporter->export($iterableValue);
                        } elseif ($event instanceof ReceivedEvent) {
                            $iterableValue = $event->value();

                            $renderedIterableEvents[] =
                                '        - Received ' .
                                $this->exporter->export($iterableValue);
                        } elseif (
                            $event instanceof ReceivedExceptionEvent
                        ) {
                            $iterableException = $event->exception();

                            $renderedIterableEvents[] =
                                '        - Received exception ' .
                                $this->exporter->export($iterableException);
                        }
                    }

                    $endEvent = $call->endEvent();

                    if (empty($iterableEvents)) {
                        if ($callIsRelevant) {
                            if ($isNever) {
                                $eventResult = $this->pass;
                            } else {
                                $eventResult = $this->fail;
                            }
                        } else {
                            $eventResult = '-';
                        }

                        $renderedIterableEvents[] =
                            '        ' . $eventResult .
                            ' Never started iterating';
                    } elseif ($endEvent instanceof ConsumedEvent) {
                        $renderedIterableEvents[] =
                            '        - Finished iterating';
                    } elseif ($endEvent instanceof ReturnedEvent) {
                        $iterableValue = $endEvent->value();
                        $renderedIterableValue =
                            $this->exporter->export($iterableValue);

                        $eventIsMatch =
                            !$value || $value->matches($iterableValue);

                        if ($eventIsMatch) {
                            ++$matchCount;
                            $isMatchingCall = true;
                        } elseif ($value instanceof EqualToMatcher) {
                            $renderedIterableValue =
                                $this->differenceEngine->difference(
                                    $renderedValue,
                                    $renderedIterableValue
                                );
                        }

                        if ($eventIsMatch xor $isNever) {
                            $eventResult = $this->pass;
                        } else {
                            $eventResult = $this->fail;
                        }

                        $renderedIterableEvents[] =
                            '        ' . $eventResult . ' Returned ' .
                            $renderedIterableValue;
                    } elseif ($endEvent instanceof ThrewEvent) {
                        if ($isNever) {
                            $eventResult = $this->pass;
                        } else {
                            $eventResult = $this->fail;
                        }

                        $eventException = $endEvent->exception();

                        $renderedIterableEvents[] =
                            '        ' . $eventResult . ' Threw ' .
                            $this->exporter->export($eventException);
                    } else {
                        if ($callIsRelevant) {
                            if ($isNever) {
                                $eventResult = $this->pass;
                            } else {
                                $eventResult = $this->fail;
                            }
                        } else {
                            $eventResult = '-';
                        }

                        $renderedIterableEvents[] =
                            '        ' . $eventResult .
                            ' Never finished iterating';
                    }

                    $renderedResponse =
                        'Returned ' . $this->exporter->export($returnValue, 0) .
                        ', then:' . $callEnd . PHP_EOL . $callStart .
                        implode(
                            $callEnd . PHP_EOL . $callStart,
                            $renderedIterableEvents
                        );
                } else {
                    $renderedResponse =
                        'Returned ' . $this->exporter->export($returnValue);
                }
            } elseif ($responseEvent instanceof ThrewEvent) {
                $exception = $responseEvent->exception();
                $renderedResponse =
                    'Threw ' . $this->exporter->export($exception);
            } else {
                $renderedResponse = 'Never responded';
            }

            if ($callIsRelevant) {
                if ($isMatchingCall xor $isNever) {
                    $renderedResult = $this->pass;
                } else {
                    $renderedResult = $this->fail;
                }
            } else {
                $renderedResult = '-';
            }

            $renderedCalls[] =
                $callStart . $renderedResult . ' Call #' . $call->index() .
                ' - ' . $renderedCallee .
                '(' . implode(', ', $renderedArguments) . '):' .
                $callEnd . PHP_EOL .
                $callStart . '    ' . $renderedResult . ' ' .
                $renderedResponse . $callEnd;
        }

        $actual = PHP_EOL . implode(PHP_EOL, $renderedCalls);

        if ($isCall) {
            $cardinality = '';
        } else {
            $cardinality = $this->renderCardinality(
                $minimum,
                $maximum,
                $matchCount,
                $totalCount,
                $totalCount,
                false
            );
        }

        return $this->reset . $expected . $cardinality . $actual;
    }

    /**
     * Render a failed generator threw() verification.
     *
     * @param Spy|Call                            $subject     The subject.
     * @param Cardinality                         $cardinality The cardinality.
     * @param Matcher|Exception|Error|string|null $type        The type of exception.
     *
     * @return string The rendered failure message.
     */
    public function renderGeneratorThrew(
        $subject,
        Cardinality $cardinality,
        $type
    ) {
        $isCall = $subject instanceof Call;

        if ($isCall) {
            $calls = array($subject);
            $renderedCallee =
                $this->exporter->exportCallable($subject->callback());
        } else {
            $calls = $subject->allCalls();
            $renderedCallee = $this->exporter->exportCallable($subject);
        }

        $renderedSubject = $this->bold . $renderedCallee . $this->reset;

        $minimum = $cardinality->minimum();
        $maximum = $cardinality->maximum();
        $isNever = null !== $maximum && $maximum < 1;

        if ($isCall) {
            $totalCount = 1;
            $iterableCount = 1;
            $renderedIterableCount = '';
        } else {
            $iterableCount = 0;

            foreach ($calls as $call) {
                if ($call->isGenerator()) {
                    ++$iterableCount;
                }
            }

            $totalCount = $iterableCount;

            if ($cardinality->matches($iterableCount, $iterableCount)) {
                $iterableResultStart = $this->passStart;
            } else {
                $iterableResultStart = $this->failStart;
            }

            $matchOrMatches = 1 === $iterableCount ? 'match' : 'matches';
            $renderedIterableCount =
                ' ' . $iterableResultStart . $this->faint .
                '(' . $iterableCount . ' ' . $matchOrMatches . ')' .
                $this->reset;
        }

        if ($iterableCount xor $isNever) {
            $iterableResult = $this->pass;
        } else {
            $iterableResult = $this->fail;
        }

        if ($type instanceof Matcher) {
            $renderedType = $type->describe($this->exporter);
        } elseif (is_string($type)) {
            $renderedType = $type;
        } else {
            $renderedType = '<any>';
        }

        $renderedCriteria =
            'behave like:' . PHP_EOL . '    ' . $iterableResult .
            ' Returned Generator, then:' . $renderedIterableCount . PHP_EOL .
            '        ' . $this->fail . ' Threw ' . $renderedType;

        if ($isCall) {
            if ($isNever) {
                $expected =
                    'Expected ' . $renderedSubject .
                    ' call #' . $subject->index() .
                    ' not to ' . $renderedCriteria;
            } else {
                $expected =
                    'Expected ' . $renderedSubject .
                    ' call #' . $subject->index() .
                    ' to ' . $renderedCriteria;
            }
        } else {
            if ($isNever) {
                $expected =
                    'Expected ' . $renderedSubject .
                    ' generator calls not to ' . $renderedCriteria;
            } elseif ($cardinality->isAlways()) {
                $expected =
                    'Expected all ' . $renderedSubject .
                    ' generator calls to ' . $renderedCriteria;
            } else {
                $expected =
                    'Expected ' . $renderedSubject .
                    ' generator calls to ' . $renderedCriteria;
            }
        }

        $renderedCalls = array();
        $matchCount = 0;

        foreach ($calls as $call) {
            $callIsRelevant = $call->isGenerator();

            if ($callIsRelevant) {
                $callStart = '';
                $callEnd = '';
            } else {
                $callStart = $this->faint;
                $callEnd = $this->reset;
            }

            $isMatchingCall = false;
            $renderedArguments = array();

            foreach ($call->arguments()->all() as $argument) {
                $renderedArguments[] = $this->exporter->export($argument, 0);
            }

            $responseEvent = $call->responseEvent();

            if ($responseEvent instanceof ReturnedEvent) {
                $returnValue = $responseEvent->value();

                if (
                    is_array($returnValue) ||
                    $returnValue instanceof Traversable
                ) {
                    $iterableEvents = $call->iterableEvents();
                    $renderedIterableEvents = array();

                    foreach ($iterableEvents as $event) {
                        if ($event instanceof UsedEvent) {
                            $renderedIterableEvents[] =
                                '        - Started iterating';
                        } elseif ($event instanceof ProducedEvent) {
                            $iterableKey = $event->key();
                            $iterableValue = $event->value();

                            $renderedIterableEvents[] =
                                '        - Produced ' .
                                $this->exporter->export($iterableKey) .
                                ' => ' .
                                $this->exporter->export($iterableValue);
                        } elseif ($event instanceof ReceivedEvent) {
                            $iterableValue = $event->value();

                            $renderedIterableEvents[] =
                                '        - Received ' .
                                $this->exporter->export($iterableValue);
                        } elseif (
                            $event instanceof ReceivedExceptionEvent
                        ) {
                            $iterableException = $event->exception();

                            $renderedIterableEvents[] =
                                '        - Received exception ' .
                                $this->exporter->export($iterableException);
                        }
                    }

                    $endEvent = $call->endEvent();

                    if (empty($iterableEvents)) {
                        if ($callIsRelevant) {
                            if ($isNever) {
                                $eventResult = $this->pass;
                            } else {
                                $eventResult = $this->fail;
                            }
                        } else {
                            $eventResult = '-';
                        }

                        $renderedIterableEvents[] =
                            '        ' . $eventResult .
                            ' Never started iterating';
                    } elseif ($endEvent instanceof ConsumedEvent) {
                        $renderedIterableEvents[] =
                            '        - Finished iterating';
                    } elseif ($endEvent instanceof ReturnedEvent) {
                        $iterableValue = $endEvent->value();

                        if ($isNever) {
                            $eventResult = $this->pass;
                        } else {
                            $eventResult = $this->fail;
                        }

                        $renderedIterableEvents[] =
                            '        ' . $eventResult . ' Returned ' .
                            $this->exporter->export($iterableValue);
                    } elseif ($endEvent instanceof ThrewEvent) {
                        $iterableException = $endEvent->exception();
                        $renderedIterableException =
                            $this->exporter->export($iterableException);

                        if ($type instanceof Matcher) {
                            $eventIsMatch =
                                $type->matches($iterableException);
                        } elseif (is_string($type)) {
                            $eventIsMatch = is_a($iterableException, $type);
                        } else {
                            $eventIsMatch = true;
                        }

                        if ($eventIsMatch) {
                            ++$matchCount;
                            $isMatchingCall = true;
                        } elseif ($type instanceof EqualToMatcher) {
                            $renderedIterableException =
                                $this->differenceEngine->difference(
                                    $renderedType,
                                    $renderedIterableException
                                );
                        }

                        if ($eventIsMatch xor $isNever) {
                            $eventResult = $this->pass;
                        } else {
                            $eventResult = $this->fail;
                        }

                        $renderedIterableEvents[] =
                            '        ' . $eventResult .
                            ' Threw ' . $renderedIterableException;
                    } else {
                        if ($callIsRelevant) {
                            if ($isNever) {
                                $eventResult = $this->pass;
                            } else {
                                $eventResult = $this->fail;
                            }
                        } else {
                            $eventResult = '-';
                        }

                        $renderedIterableEvents[] =
                            '        ' . $eventResult .
                            ' Never finished iterating';
                    }

                    $renderedResponse =
                        'Returned ' . $this->exporter->export($returnValue, 0) .
                        ', then:' . $callEnd . PHP_EOL . $callStart .
                        implode(
                            $callEnd . PHP_EOL . $callStart,
                            $renderedIterableEvents
                        );
                } else {
                    $renderedResponse =
                        'Returned ' . $this->exporter->export($returnValue);
                }
            } elseif ($responseEvent instanceof ThrewEvent) {
                $exception = $responseEvent->exception();
                $renderedResponse =
                    'Threw ' . $this->exporter->export($exception);
            } else {
                $renderedResponse = 'Never responded';
            }

            if ($callIsRelevant) {
                if ($isMatchingCall xor $isNever) {
                    $renderedResult = $this->pass;
                } else {
                    $renderedResult = $this->fail;
                }
            } else {
                $renderedResult = '-';
            }

            $renderedCalls[] =
                $callStart . $renderedResult . ' Call #' . $call->index() .
                ' - ' . $renderedCallee .
                '(' . implode(', ', $renderedArguments) . '):' .
                $callEnd . PHP_EOL .
                $callStart . '    ' . $renderedResult . ' ' .
                $renderedResponse . $callEnd;
        }

        $actual = PHP_EOL . implode(PHP_EOL, $renderedCalls);

        if ($isCall) {
            $cardinality = '';
        } else {
            $cardinality = $this->renderCardinality(
                $minimum,
                $maximum,
                $matchCount,
                $totalCount,
                $totalCount,
                false
            );
        }

        return $this->reset . $expected . $cardinality . $actual;
    }

    /**
     * Render a failed noInteraction() verification.
     *
     * @param Handle      $handle The handle.
     * @param array<Call> $calls  The calls.
     *
     * @return string The rendered failure message.
     */
    public function renderNoInteraction(Handle $handle, array $calls)
    {
        $class = $handle->clazz();

        if ($parentClass = $class->getParentClass()) {
            $class = $parentClass;
        }

        $atoms = explode('\\', $class->getName());
        $renderedHandle = array_pop($atoms);

        if ($handle instanceof InstanceHandle) {
            $label = $handle->label();

            if (null !== $label) {
                $renderedHandle .= '[' . $label . ']';
            }
        } else {
            $renderedHandle .= '[static]';
        }

        usort($calls, 'Eloquent\Phony\Call\CallData::compareSequential');
        $renderedCalls = array();

        foreach ($calls as $index => $call) {
            $renderedArguments = array();

            foreach ($call->arguments()->all() as $argument) {
                $renderedArguments[] = $this->exporter->export($argument);
            }

            $renderedCalls[] =
                '    ' . $this->fail .
                ' ' . $this->exporter->exportCallable($call->callback()) .
                '(' . implode(', ', $renderedArguments) . ')';
        }

        return $this->reset . 'Expected no interaction with ' .
            $this->bold . $renderedHandle . $this->reset .
            '. Calls:' . PHP_EOL . implode(PHP_EOL, $renderedCalls);
    }

    /**
     * Render a failed inOrder() verification.
     *
     * @param array<Event> $expected The expected events.
     * @param array<Event> $actual   The actual events.
     *
     * @return string The rendered failure message.
     */
    public function renderInOrder(array $expected, array $actual)
    {
        if (empty($expected)) {
            return $this->reset . 'Expected events.' . PHP_EOL .
                $this->failStart . 'No events recorded.' . $this->reset;
        }

        $from = $this->renderEvents($expected);
        $to = $this->renderEvents($actual);

        $matcher = new DifferenceSequenceMatcher($from, $to);
        $diff = array();

        foreach ($matcher->getOpcodes() as $opcode) {
            list($tag, $i1, $i2, $j1, $j2) = $opcode;

            if ($tag === 'equal') {
                foreach (array_slice($from, $i1, $i2 - $i1) as $event) {
                    $diff[] = '    ' . $this->pass . '   ' . $event;
                }
            } else {
                if ($tag === 'replace' || $tag === 'delete') {
                    foreach (array_slice($from, $i1, $i2 - $i1) as $event) {
                        $diff[] =
                            '    ' . $this->fail . ' ' .
                            $this->removeStart . $event . $this->removeEnd;
                    }
                }

                if ($tag === 'replace' || $tag === 'insert') {
                    foreach (array_slice($to, $j1, $j2 - $j1) as $event) {
                        $diff[] =
                            '    - ' . $this->addStart . $event . $this->addEnd;
                    }
                }
            }
        }

        $renderedExpected = array();

        foreach ($from as $event) {
            $renderedExpected[] = '    - ' . $event;
        }

        $renderedActual = array();

        foreach ($to as $event) {
            $renderedActual[] = '    - ' . $event;
        }

        return $this->reset . 'Expected events in order:' . PHP_EOL .
            implode(PHP_EOL, $renderedExpected) . PHP_EOL .
            'Actual order:' . PHP_EOL .
            implode(PHP_EOL, $renderedActual) . PHP_EOL .
            'Difference:' . PHP_EOL .
            implode(PHP_EOL, $diff);
    }

    /**
     * Render a value.
     *
     * @param mixed $value The value.
     *
     * @return string The rendered value.
     */
    public function renderValue($value)
    {
        return $this->exporter->export($value);
    }

    /**
     * Render a sequence of matchers.
     *
     * @param array<Matchable> $matchers The matchers.
     *
     * @return string The rendered matchers.
     */
    public function renderMatchers(array $matchers)
    {
        if (count($matchers) < 1) {
            return '<none>';
        }

        $rendered = array();

        foreach ($matchers as $matcher) {
            $rendered[] = $matcher->describe($this->exporter);
        }

        return implode(', ', $rendered);
    }

    private function renderEvents($events)
    {
        $rendered = array();

        foreach ($events as $event) {
            if ($event instanceof CallEvent) {
                $call = $event->call();
                $renderedArguments = array();

                foreach ($call->arguments()->all() as $argument) {
                    $renderedArguments[] =
                        $this->exporter->export($argument);
                }

                $call =
                    $this->exporter->exportCallable($call->callback()) .
                    '(' . implode(', ', $renderedArguments) . ')';
            }

            if ($event instanceof Call) {
                $renderedArguments = array();

                foreach ($event->arguments()->all() as $argument) {
                    $renderedArguments[] = $this->exporter->export($argument);
                }

                $rendered[] =
                    'Called ' .
                    $this->exporter->exportCallable($event->callback()) .
                    '(' . implode(', ', $renderedArguments) . ')';
            } elseif ($event instanceof CalledEvent) {
                $rendered[] = 'Called ' . $call;
            } elseif ($event instanceof ReturnedEvent) {
                $eventValue = $event->value();

                $rendered[] =
                    'Returned ' . $this->exporter->export($eventValue) .
                    ' from ' . $call;
            } elseif ($event instanceof ThrewEvent) {
                $eventException = $event->exception();

                $rendered[] =
                    'Threw ' . $this->exporter->export($eventException) .
                    ' from ' . $call;
            } elseif ($event instanceof UsedEvent) {
                $rendered[] = $call . ' started iterating';
            } elseif ($event instanceof ProducedEvent) {
                $eventKey = $event->key();
                $eventValue = $event->value();

                $rendered[] =
                    'Produced ' . $this->exporter->export($eventKey) .
                    ' => ' . $this->exporter->export($eventValue) .
                    ' from ' . $call;
            } elseif ($event instanceof ReceivedEvent) {
                $eventValue = $event->value();

                $rendered[] =
                    'Received ' . $this->exporter->export($eventValue) .
                    ' in ' . $call;
            } elseif ($event instanceof ReceivedExceptionEvent) {
                $eventException = $event->exception();

                $rendered[] =
                    'Received exception ' .
                    $this->exporter->export($eventException) . ' in ' . $call;
            } elseif ($event instanceof ConsumedEvent) {
                $rendered[] = $call . ' finished iterating';
            } else {
                $eventClass = get_class($event);

                $rendered[] = $this->exporter->export($eventClass) . ' event';
            }
        }

        return $rendered;
    }

    private function renderCardinality(
        $minimum,
        $maximum,
        $matchCount,
        $totalCount,
        $callCount,
        $isFailureCause
    ) {
        if (!$minimum) {
            if (0 === $maximum) {
                $expected = '';
            } else {
                $expected = 'Up to ' . $maximum . ' allowed. ';
            }
        } elseif (null === $maximum) {
            if (1 === $minimum) {
                $expected = '';
            } else {
                $expected = 'At least ' . $minimum . ' required. ';
            }
        } elseif ($minimum === $maximum) {
            $expected = 'Exactly ' . $minimum . ' required. ';
        } else {
            $expected =
                'Between ' . $minimum . ' and ' . $maximum . ' allowed. ';
        }

        if ($callCount) {
            $actual = 'Matched ' . $matchCount . ' of ' . $totalCount . ':';
        } else {
            $isFailureCause = true;
            $actual = 'Never called.';
        }

        if ($isFailureCause || $expected) {
            return
                PHP_EOL . $this->failStart . $expected . $actual . $this->reset;
        }

        return PHP_EOL . $expected . $actual;
    }

    const PASS = "\xe2\x9C\x93";
    const FAIL = "\xe2\x9C\x97";

    private static $instance;
    private $matcherVerifier;
    private $exporter;
    private $differenceEngine;
    private $featureDetector;
    private $reset;
    private $bold;
    private $faint;
    private $passStart;
    private $failStart;
    private $pass;
    private $fail;
    private $addStart;
    private $addEnd;
    private $removeStart;
    private $removeEnd;
}

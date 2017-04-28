<?php

/*
 * This file is part of the Phony package.
 *
 * Copyright © 2016 Erin Millard
 *
 * For the full copyright and license information, please view the LICENSE file
 * that was distributed with this source code.
 */

namespace Eloquent\Phony\Simpletest;

use Eloquent\Phony\Assertion\AssertionRecorder;
use Eloquent\Phony\Assertion\Exception\AssertionException;
use Eloquent\Phony\Call\CallVerifierFactory;
use Eloquent\Phony\Event\Event;
use Eloquent\Phony\Event\EventCollection;
use Eloquent\Phony\Event\EventSequence;
use Exception;
use SimpleTest;
use SimpleTestContext;

/**
 * An assertion recorder for SimpleTest.
 *
 * @codeCoverageIgnore
 */
class SimpletestAssertionRecorder implements AssertionRecorder
{
    /**
     * Get the static instance of this recorder.
     *
     * @return AssertionRecorder The static recorder.
     */
    public static function instance()
    {
        if (!self::$instance) {
            self::$instance = new self(SimpleTest::getContext());
        }

        return self::$instance;
    }

    /**
     * Construct a new SimpleTest assertion recorder.
     *
     * @param SimpleTestContext $simpletestContext The SimpleTest context to use.
     */
    public function __construct(SimpleTestContext $simpletestContext)
    {
        $this->simpletestContext = $simpletestContext;
    }

    /**
     * Set the call verifier factory.
     *
     * @param CallVerifierFactory $callVerifierFactory The call verifier factory to use.
     */
    public function setCallVerifierFactory(
        CallVerifierFactory $callVerifierFactory
    ) {
        $this->callVerifierFactory = $callVerifierFactory;
    }

    /**
     * Record that a successful assertion occurred.
     *
     * @param array<Event> $events The events.
     *
     * @return EventCollection The result.
     */
    public function createSuccess(array $events = array())
    {
        $this->simpletestContext->getReporter()->paintPass('');

        return new EventSequence($events, $this->callVerifierFactory);
    }

    /**
     * Record that a successful assertion occurred.
     *
     * @param EventCollection $events The events.
     *
     * @return EventCollection The result.
     */
    public function createSuccessFromEventCollection(EventCollection $events)
    {
        $this->simpletestContext->getReporter()->paintPass('');

        return $events;
    }

    /**
     * Create a new assertion failure exception.
     *
     * @param string $description The failure description.
     *
     * @throws Exception If this recorder throws exceptions.
     */
    public function createFailure($description)
    {
        $flags = 0;

        if (defined('DEBUG_BACKTRACE_IGNORE_ARGS')) {
            $flags = DEBUG_BACKTRACE_IGNORE_ARGS;
        }

        $call = AssertionException::tracePhonyCall(debug_backtrace($flags));

        if ($call && isset($call['file']) && isset($call['line'])) {
            $description .= PHP_EOL . "at [$call[file] line $call[line]]";
        }

        $this->simpletestContext->getReporter()
            ->paintFail(preg_replace('/(\R)/', '$1   ', $description));
    }

    private static $instance;
    private $simpletestContext;
    private $callVerifierFactory;
}

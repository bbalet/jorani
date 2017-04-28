<?php

/*
 * This file is part of the Phony package.
 *
 * Copyright © 2016 Erin Millard
 *
 * For the full copyright and license information, please view the LICENSE file
 * that was distributed with this source code.
 */

namespace Eloquent\Phony\Call\Event;

use Eloquent\Phony\Call\Arguments;
use Eloquent\Phony\Clock\Clock;
use Eloquent\Phony\Clock\SystemClock;
use Eloquent\Phony\Sequencer\Sequencer;
use Error;
use Exception;

/**
 * Creates call events.
 */
class CallEventFactory
{
    /**
     * Get the static instance of this factory.
     *
     * @return CallEventFactory The static factory.
     */
    public static function instance()
    {
        if (!self::$instance) {
            self::$instance = new self(
                Sequencer::sequence('event-sequence-number'),
                SystemClock::instance()
            );
        }

        return self::$instance;
    }

    /**
     * Construct a new call event factory.
     *
     * @param Sequencer $sequencer The sequencer to use.
     * @param Clock     $clock     The clock to use.
     */
    public function __construct(Sequencer $sequencer, Clock $clock)
    {
        $this->sequencer = $sequencer;
        $this->clock = $clock;
    }

    /**
     * Create a new 'called' event.
     *
     * @param callable  $callback  The callback.
     * @param Arguments $arguments The arguments.
     *
     * @return CalledEvent The newly created event.
     */
    public function createCalled($callback, Arguments $arguments)
    {
        return new CalledEvent(
            $this->sequencer->next(),
            $this->clock->time(),
            $callback,
            $arguments
        );
    }

    /**
     * Create a new 'returned' event.
     *
     * @param mixed $value The return value.
     *
     * @return ReturnedEvent The newly created event.
     */
    public function createReturned($value)
    {
        return new ReturnedEvent(
            $this->sequencer->next(),
            $this->clock->time(),
            $value
        );
    }

    /**
     * Create a new 'thrown' event.
     *
     * @param Exception|Error $exception The thrown exception.
     *
     * @return ThrewEvent The newly created event.
     */
    public function createThrew($exception)
    {
        return new ThrewEvent(
            $this->sequencer->next(),
            $this->clock->time(),
            $exception
        );
    }

    /**
     * Create a new 'used' event.
     *
     * @return UsedEvent The newly created event.
     */
    public function createUsed()
    {
        return new UsedEvent($this->sequencer->next(), $this->clock->time());
    }

    /**
     * Create a new 'produced' event.
     *
     * @param mixed $key   The produced key.
     * @param mixed $value The produced value.
     *
     * @return ProducedEvent The newly created event.
     */
    public function createProduced($key, $value)
    {
        return new ProducedEvent(
            $this->sequencer->next(),
            $this->clock->time(),
            $key,
            $value
        );
    }

    /**
     * Create a new 'received' event.
     *
     * @param mixed $value The received value.
     *
     * @return ReceivedEvent The newly created event.
     */
    public function createReceived($value)
    {
        return new ReceivedEvent(
            $this->sequencer->next(),
            $this->clock->time(),
            $value
        );
    }

    /**
     * Create a new 'received exception' event.
     *
     * @param Exception|Error $exception The received exception.
     *
     * @return ReceivedExceptionEvent The newly created event.
     */
    public function createReceivedException($exception)
    {
        return new ReceivedExceptionEvent(
            $this->sequencer->next(),
            $this->clock->time(),
            $exception
        );
    }

    /**
     * Create a new 'consumed' event.
     *
     * @return ConsumedEvent The newly created event.
     */
    public function createConsumed()
    {
        return new ConsumedEvent(
            $this->sequencer->next(),
            $this->clock->time()
        );
    }

    private static $instance;
    private $sequencer;
    private $clock;
}

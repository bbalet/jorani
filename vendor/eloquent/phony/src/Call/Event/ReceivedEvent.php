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

/**
 * Represents a value received by a generator.
 */
class ReceivedEvent extends AbstractCallEvent implements IterableEvent
{
    /**
     * Construct a 'received' event.
     *
     * @param int   $sequenceNumber The sequence number.
     * @param float $time           The time at which the event occurred, in seconds since the Unix epoch.
     * @param mixed $value          The received value.
     */
    public function __construct($sequenceNumber, $time, $value)
    {
        parent::__construct($sequenceNumber, $time);

        $this->value = $value;
    }

    /**
     * Get the received value.
     *
     * @return mixed The received value.
     */
    public function value()
    {
        return $this->value;
    }

    private $value;
}

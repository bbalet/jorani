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
 * Represents a produced key-value pair.
 */
class ProducedEvent extends AbstractCallEvent implements IterableEvent
{
    /**
     * Construct a 'produced' event.
     *
     * @param int   $sequenceNumber The sequence number.
     * @param float $time           The time at which the event occurred, in seconds since the Unix epoch.
     * @param mixed $key            The produced key.
     * @param mixed $value          The produced value.
     */
    public function __construct($sequenceNumber, $time, $key, $value)
    {
        parent::__construct($sequenceNumber, $time);

        $this->key = $key;
        $this->value = $value;
    }

    /**
     * Get the produced key.
     *
     * @return mixed The produced key.
     */
    public function key()
    {
        return $this->key;
    }

    /**
     * Get the produced value.
     *
     * @return mixed The produced value.
     */
    public function value()
    {
        return $this->value;
    }

    private $key;
    private $value;
}

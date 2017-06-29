<?php

/*
 * This file is part of the Phony package.
 *
 * Copyright © 2016 Erin Millard
 *
 * For the full copyright and license information, please view the LICENSE file
 * that was distributed with this source code.
 */

namespace Eloquent\Phony\Clock;

/**
 * Provides access to the system clock.
 */
class SystemClock implements Clock
{
    /**
     * Get the static instance of this clock.
     *
     * @return Clock The static clock.
     */
    public static function instance()
    {
        if (!self::$instance) {
            self::$instance = new self('microtime');
        }

        return self::$instance;
    }

    /**
     * Construct a new system clock.
     *
     * @param callable $microtime The implementation of microtime() to use.
     */
    public function __construct($microtime)
    {
        $this->microtime = $microtime;
    }

    /**
     * Get the current time.
     *
     * @return float The current time.
     */
    public function time()
    {
        $microtime = $this->microtime;

        return $microtime(true);
    }

    private static $instance;
    private $microtime;
}

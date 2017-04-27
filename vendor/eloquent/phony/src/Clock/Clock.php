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
 * The interface implemented by clocks.
 */
interface Clock
{
    /**
     * Get the current time.
     *
     * @return float The current time.
     */
    public function time();
}

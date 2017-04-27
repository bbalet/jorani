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

use Eloquent\Phony\Call\Call;
use Eloquent\Phony\Event\Event;

/**
 * The interface implemented by call events.
 */
interface CallEvent extends Event
{
    /**
     * Set the call.
     *
     * @param Call $call The call.
     *
     * @return $this This event.
     */
    public function setCall(Call $call);

    /**
     * Get the call.
     *
     * @return Call|null The call, or null if no call has been set.
     */
    public function call();
}

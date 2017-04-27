<?php

/*
 * This file is part of the Phony package.
 *
 * Copyright © 2016 Erin Millard
 *
 * For the full copyright and license information, please view the LICENSE file
 * that was distributed with this source code.
 */

namespace Eloquent\Phony\Mock\Method;

use Eloquent\Phony\Mock\Handle\Handle;
use Eloquent\Phony\Mock\Mock;

/**
 * The interface implemented by wrapped methods.
 */
interface WrappedMethod
{
    /**
     * Get the name.
     *
     * @return string The name.
     */
    public function name();

    /**
     * Get the handle.
     *
     * @return Handle The handle.
     */
    public function handle();

    /**
     * Get the mock.
     *
     * @return Mock|null The mock.
     */
    public function mock();
}

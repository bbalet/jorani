<?php

/*
 * This file is part of the Phony package.
 *
 * Copyright © 2016 Erin Millard
 *
 * For the full copyright and license information, please view the LICENSE file
 * that was distributed with this source code.
 */

namespace Eloquent\Phony\Invocation;

use Eloquent\Phony\Call\Arguments;
use Error;
use Exception;

/**
 * Invokes callbacks, maintaining reference parameters.
 */
class Invoker
{
    /**
     * Get the static instance of this invoker.
     *
     * @return Invoker The static invoker.
     */
    public static function instance()
    {
        if (!self::$instance) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * Calls a callback, maintaining reference parameters.
     *
     * @param callable  $callback  The callback.
     * @param Arguments $arguments The arguments.
     *
     * @return mixed           The result of invocation.
     * @throws Exception|Error If an error occurs.
     */
    public function callWith($callback, Arguments $arguments)
    {
        if ($callback instanceof Invocable) {
            return $callback->invokeWith($arguments);
        }

        return call_user_func_array($callback, $arguments->all());
    }

    private static $instance;
}

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
 * The interface implemented by invocables.
 */
interface Invocable
{
    /**
     * Invoke this object.
     *
     * This method supports reference parameters.
     *
     * @param Arguments|array $arguments The arguments.
     *
     * @return mixed           The result of invocation.
     * @throws Exception|Error If an error occurs.
     */
    public function invokeWith($arguments = array());

    /**
     * Invoke this object.
     *
     * @param mixed ...$arguments The arguments.
     *
     * @return mixed           The result of invocation.
     * @throws Exception|Error If an error occurs.
     */
    public function invoke();

    /**
     * Invoke this object.
     *
     * @param mixed ...$arguments The arguments.
     *
     * @return mixed           The result of invocation.
     * @throws Exception|Error If an error occurs.
     */
    public function __invoke();
}

<?php

/*
 * This file is part of the Phony package.
 *
 * Copyright © 2016 Erin Millard
 *
 * For the full copyright and license information, please view the LICENSE file
 * that was distributed with this source code.
 */

namespace Eloquent\Phony\Exporter;

/**
 * The interface implemented by exporters.
 */
interface Exporter
{
    /**
     * Set the default depth.
     *
     * Negative depths are treated as infinite depth.
     *
     * @param int $depth The depth.
     *
     * @return int The previous depth.
     */
    public function setDepth($depth);

    /**
     * Export the supplied value.
     *
     * Negative depths are treated as infinite depth.
     *
     * @param mixed    &$value The value.
     * @param int|null $depth  The depth, or null to use the default.
     *
     * @return string The exported value.
     */
    public function export(&$value, $depth = null);

    /**
     * Export a string representation of a callable value.
     *
     * @param callable $callback The callable.
     *
     * @return string The exported callable.
     */
    public function exportCallable($callback);
}

<?php

/*
 * This file is part of the Phony package.
 *
 * Copyright © 2016 Erin Millard
 *
 * For the full copyright and license information, please view the LICENSE file
 * that was distributed with this source code.
 */

namespace Eloquent\Phony\Matcher;

use Eloquent\Phony\Exporter\Exporter;

/**
 * A matcher that always returns true.
 */
class AnyMatcher implements Matcher
{
    /**
     * Get the static instance of this matcher.
     *
     * @return Matcher The static matcher.
     */
    public static function instance()
    {
        if (!self::$instance) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * Returns `true` if `$value` matches this matcher's criteria.
     *
     * @param mixed $value The value to check.
     *
     * @return bool True if the value matches.
     */
    public function matches($value)
    {
        return true;
    }

    /**
     * Describe this matcher.
     *
     * @param Exporter|null $exporter The exporter to use.
     *
     * @return string The description.
     */
    public function describe(Exporter $exporter = null)
    {
        return '<any>';
    }

    /**
     * Describe this matcher.
     *
     * @return string The description.
     */
    public function __toString()
    {
        return '<any>';
    }

    private static $instance;
}

<?php

/*
 * This file is part of the Phony package.
 *
 * Copyright © 2016 Erin Millard
 *
 * For the full copyright and license information, please view the LICENSE file
 * that was distributed with this source code.
 */

namespace Eloquent\Phony\Integration;

use Eloquent\Phony\Matcher\Matchable;
use Eloquent\Phony\Matcher\MatcherDriver;

/**
 * A matcher driver for Mockery matchers.
 */
class MockeryMatcherDriver implements MatcherDriver
{
    /**
     * Get the static instance of this driver.
     *
     * @return MatcherDriver The static driver.
     */
    public static function instance()
    {
        if (!self::$instance) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * Returns true if this matcher driver's classes or interfaces exist.
     *
     * @return bool True if available.
     */
    public function isAvailable()
    {
        return class_exists('Mockery\Matcher\MatcherAbstract');
    }

    /**
     * Get the supported matcher class names.
     *
     * @return array<string> The matcher class names.
     */
    public function matcherClassNames()
    {
        return array('Mockery\Matcher\MatcherAbstract');
    }

    /**
     * Wrap the supplied third party matcher.
     *
     * @param object $matcher The matcher to wrap.
     *
     * @return Matchable The wrapped matcher.
     */
    public function wrapMatcher($matcher)
    {
        return new MockeryMatcher($matcher);
    }

    private static $instance;
}

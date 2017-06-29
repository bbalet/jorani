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
use Eloquent\Phony\Matcher\WildcardMatcher;

/**
 * A matcher driver for Prophecy tokens.
 */
class ProphecyMatcherDriver implements MatcherDriver
{
    /**
     * Get the static instance of this driver.
     *
     * @return MatcherDriver The static driver.
     */
    public static function instance()
    {
        if (!self::$instance) {
            self::$instance = new self(WildcardMatcher::instance());
        }

        return self::$instance;
    }

    /**
     * Construct a new Prophecy matcher driver.
     *
     * @param WildcardMatcher $wildcard The wildcard matcher to use.
     */
    public function __construct(WildcardMatcher $wildcard)
    {
        $this->wildcard = $wildcard;
    }

    /**
     * Returns true if this matcher driver's classes or interfaces exist.
     *
     * @return bool True if available.
     */
    public function isAvailable()
    {
        return interface_exists('Prophecy\Argument\Token\TokenInterface');
    }

    /**
     * Get the supported matcher class names.
     *
     * @return array<string> The matcher class names.
     */
    public function matcherClassNames()
    {
        return array('Prophecy\Argument\Token\TokenInterface');
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
        if (is_a($matcher, 'Prophecy\Argument\Token\AnyValuesToken')) {
            return $this->wildcard;
        }

        return new ProphecyMatcher($matcher);
    }

    private static $instance;
    private $wildcard;
}

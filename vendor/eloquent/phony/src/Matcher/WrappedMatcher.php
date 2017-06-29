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
 * Wraps a typical third party matcher.
 */
class WrappedMatcher implements Matcher
{
    /**
     * Construct a new wrapped matcher.
     *
     * @param object $matcher The matcher to wrap.
     */
    public function __construct($matcher)
    {
        $this->matcher = $matcher;
    }

    /**
     * Get the wrapped matcher.
     *
     * @return object The matcher.
     */
    public function matcher()
    {
        return $this->matcher;
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
        return (bool) $this->matcher->matches($value);
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
        return '<' . strval($this->matcher) . '>';
    }

    /**
     * Describe this matcher.
     *
     * @return string The description.
     */
    public function __toString()
    {
        return '<' . strval($this->matcher) . '>';
    }

    protected $matcher;
}

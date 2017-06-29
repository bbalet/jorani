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

use Eloquent\Phony\Matcher\WrappedMatcher;

/**
 * A matcher that wraps a Prophecy token.
 */
class ProphecyMatcher extends WrappedMatcher
{
    /**
     * Returns `true` if `$value` matches this matcher's criteria.
     *
     * @param mixed $value The value to check.
     *
     * @return bool True if the value matches.
     */
    public function matches($value)
    {
        return (bool) $this->matcher->scoreArgument($value);
    }
}

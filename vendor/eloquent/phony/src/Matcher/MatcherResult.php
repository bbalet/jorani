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

/**
 * Represents the result of matching arguments against matchers.
 */
class MatcherResult
{
    /**
     * Construct a new matcher result.
     *
     * @param bool        $isMatch         True if successful match.
     * @param array<bool> $matcherMatches  The matcher results.
     * @param array<bool> $argumentMatches The argument results.
     */
    public function __construct(
        $isMatch,
        array $matcherMatches,
        array $argumentMatches
    ) {
        $this->isMatch = $isMatch;
        $this->matcherMatches = $matcherMatches;
        $this->argumentMatches = $argumentMatches;
    }

    public $isMatch;
    public $matcherMatches;
    public $argumentMatches;
}

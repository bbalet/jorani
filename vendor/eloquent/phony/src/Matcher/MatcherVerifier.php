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
 * Verifies argument lists against matcher lists.
 */
class MatcherVerifier
{
    /**
     * Get the static instance of this verifier.
     *
     * @return MatcherVerifier The static verifier.
     */
    public static function instance()
    {
        if (!self::$instance) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * Verify that the supplied arguments match the supplied matchers.
     *
     * @param array<Matchable> $matchers  The matchers.
     * @param array            $arguments The arguments.
     *
     * @return bool True if the arguments match.
     */
    public function matches(array $matchers, array $arguments)
    {
        $pair = each($arguments);

        foreach ($matchers as $matcher) {
            if ($matcher instanceof WildcardMatcher) {
                $matchCount = 0;
                $innerMatcher = $matcher->matcher();

                while (!empty($pair) && $innerMatcher->matches($pair[1])) {
                    ++$matchCount;
                    $pair = each($arguments);
                }

                $maximumArguments = $matcher->maximumArguments();

                $isMatch =
                    (
                        null === $maximumArguments ||
                        $matchCount <= $maximumArguments
                    ) &&
                    $matchCount >= $matcher->minimumArguments();

                if (!$isMatch) {
                    return false;
                }

                continue;
            }

            if (empty($pair) || !$matcher->matches($pair[1])) {
                return false;
            } else {
                $pair = each($arguments);
            }
        }

        return false === $pair;
    }

    /**
     * Explain which of the supplied arguments match which of the supplied
     * matchers.
     *
     * @param array<Matchable> $matchers  The matchers.
     * @param array            $arguments The arguments.
     *
     * @return MatcherResult The result of matching.
     */
    public function explain(array $matchers, array $arguments)
    {
        $isMatch = true;
        $matcherMatches = array();
        $argumentMatches = array();
        $pair = each($arguments);

        foreach ($matchers as $matcher) {
            if ($matcher instanceof WildcardMatcher) {
                $matcherIsMatch = true;
                $innerMatcher = $matcher->matcher();
                $minimumArguments = $matcher->minimumArguments();
                $maximumArguments = $matcher->maximumArguments();

                for ($count = 0; $count < $minimumArguments; ++$count) {
                    if (empty($pair)) {
                        $matcherIsMatch = false;
                        $argumentMatches[] = false;

                        break;
                    }

                    if ($innerMatcher->matches($pair[1])) {
                        $argumentMatches[] = true;
                    } else {
                        $matcherIsMatch = false;
                        $argumentMatches[] = false;
                    }

                    $pair = each($arguments);
                }

                if (null === $maximumArguments) {
                    while (!empty($pair) && $innerMatcher->matches($pair[1])) {
                        $argumentMatches[] = true;
                        $pair = each($arguments);
                    }
                } else {
                    for (; $count < $maximumArguments; ++$count) {
                        if (empty($pair) || !$innerMatcher->matches($pair[1])) {
                            break;
                        }

                        $argumentMatches[] = true;
                        $pair = each($arguments);
                    }
                }

                $isMatch = $isMatch && $matcherIsMatch;
                $matcherMatches[] = $matcherIsMatch;

                continue;
            }

            $matcherIsMatch = !empty($pair) && $matcher->matches($pair[1]);

            $isMatch = $isMatch && $matcherIsMatch;
            $matcherMatches[] = $matcherIsMatch;
            $argumentMatches[] = $matcherIsMatch;
            $pair = each($arguments);
        }

        while (!empty($pair)) {
            $argumentMatches[] = false;
            $isMatch = false;
            $pair = each($arguments);
        }

        return new MatcherResult($isMatch, $matcherMatches, $argumentMatches);
    }

    private static $instance;
}

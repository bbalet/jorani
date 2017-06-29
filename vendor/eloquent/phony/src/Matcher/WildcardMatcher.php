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
 * A matcher that tests any number of arguments against another matcher.
 */
class WildcardMatcher implements Matchable
{
    /**
     * Get the static instance of this matcher.
     *
     * @return WildcardMatcher The static matcher.
     */
    public static function instance()
    {
        if (!self::$instance) {
            self::$instance = new self(AnyMatcher::instance(), 0, null);
        }

        return self::$instance;
    }

    /**
     * Construct a new wildcard matcher.
     *
     * @param Matcher  $matcher          The matcher to use for each argument.
     * @param int      $minimumArguments The minimum number of arguments.
     * @param int|null $maximumArguments The maximum number of arguments.
     */
    public function __construct(
        Matcher $matcher,
        $minimumArguments,
        $maximumArguments
    ) {
        $this->matcher = $matcher;
        $this->minimumArguments = $minimumArguments;
        $this->maximumArguments = $maximumArguments;
    }

    /**
     * Get the matcher to use for each argument.
     *
     * @return Matcher The matcher.
     */
    public function matcher()
    {
        return $this->matcher;
    }

    /**
     * Get the minimum number of arguments to match.
     *
     * @return int The minimum number of arguments.
     */
    public function minimumArguments()
    {
        return $this->minimumArguments;
    }

    /**
     * Get the maximum number of arguments to match.
     *
     * @return int|null The maximum number of arguments.
     */
    public function maximumArguments()
    {
        return $this->maximumArguments;
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
        $matcherDescription = $this->matcher->describe($exporter);

        if (0 === $this->minimumArguments) {
            if (null === $this->maximumArguments) {
                return sprintf('%s*', $matcherDescription);
            } else {
                return sprintf(
                    '%s{,%d}',
                    $matcherDescription,
                    $this->maximumArguments
                );
            }
        } elseif (null === $this->maximumArguments) {
            return sprintf(
                '%s{%d,}',
                $matcherDescription,
                $this->minimumArguments
            );
        } elseif ($this->minimumArguments === $this->maximumArguments) {
            return sprintf(
                '%s{%d}',
                $matcherDescription,
                $this->minimumArguments
            );
        }

        return sprintf(
            '%s{%d,%d}',
            $matcherDescription,
            $this->minimumArguments,
            $this->maximumArguments
        );
    }

    /**
     * Describe this matcher.
     *
     * @return string The description.
     */
    public function __toString()
    {
        return $this->describe();
    }

    private static $instance;
    private $matcher;
    private $minimumArguments;
    private $maximumArguments;
}

<?php

/*
 * This file is part of the Phony package.
 *
 * Copyright © 2016 Erin Millard
 *
 * For the full copyright and license information, please view the LICENSE file
 * that was distributed with this source code.
 */

namespace Eloquent\Phony\Verification;

use Eloquent\Phony\Verification\Exception\InvalidCardinalityException;

/**
 * The interface implemented by cardinality verifiers.
 */
interface CardinalityVerifier
{
    /**
     * Requires that the next verification never matches.
     *
     * @return $this This verifier.
     */
    public function never();

    /**
     * Requires that the next verification matches only once.
     *
     * @return $this This verifier.
     */
    public function once();

    /**
     * Requires that the next verification matches exactly two times.
     *
     * @return $this This verifier.
     */
    public function twice();

    /**
     * Requires that the next verification matches exactly three times.
     *
     * @return $this This verifier.
     */
    public function thrice();

    /**
     * Requires that the next verification matches an exact number of times.
     *
     * @param int $times The match count.
     *
     * @return $this This verifier.
     */
    public function times($times);

    /**
     * Requires that the next verification matches a number of times greater
     * than or equal to $minimum.
     *
     * @param int $minimum The minimum match count.
     *
     * @return $this This verifier.
     */
    public function atLeast($minimum);

    /**
     * Requires that the next verification matches a number of times less than
     * or equal to $maximum.
     *
     * @param int $maximum The maximum match count.
     *
     * @return $this This verifier.
     */
    public function atMost($maximum);

    /**
     * Requires that the next verification matches a number of times greater
     * than or equal to $minimum, and less than or equal to $maximum.
     *
     * @param int      $minimum The minimum match count.
     * @param int|null $maximum The maximum match count, or null for no maximum.
     *
     * @return $this                       This verifier.
     * @throws InvalidCardinalityException If the cardinality is invalid.
     */
    public function between($minimum, $maximum);

    /**
     * Requires that the next verification matches for all possible items.
     *
     * @return $this This verifier.
     */
    public function always();

    /**
     * Reset the cardinality to its default value.
     *
     * @return Cardinality The current cardinality.
     */
    public function resetCardinality();

    /**
     * Get the cardinality.
     *
     * @return Cardinality The cardinality.
     */
    public function cardinality();
}

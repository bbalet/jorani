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
 * An abstract base class for implementing cardinality verifiers.
 */
abstract class AbstractCardinalityVerifier implements CardinalityVerifier
{
    /**
     * Construct a new cardinality verifier.
     */
    public function __construct()
    {
        $this->resetCardinality();
    }

    /**
     * Requires that the next verification never matches.
     *
     * @return $this This verifier.
     */
    public function never()
    {
        $this->cardinality = new Cardinality(0, 0);

        return $this;
    }

    /**
     * Requires that the next verification matches only once.
     *
     * @return $this This verifier.
     */
    public function once()
    {
        $this->cardinality = new Cardinality(1, 1);

        return $this;
    }

    /**
     * Requires that the next verification matches exactly two times.
     *
     * @return $this This verifier.
     */
    public function twice()
    {
        $this->cardinality = new Cardinality(2, 2);

        return $this;
    }

    /**
     * Requires that the next verification matches exactly three times.
     *
     * @return $this This verifier.
     */
    public function thrice()
    {
        $this->cardinality = new Cardinality(3, 3);

        return $this;
    }

    /**
     * Requires that the next verification matches an exact number of times.
     *
     * @param int $times The match count.
     *
     * @return $this This verifier.
     */
    public function times($times)
    {
        $this->cardinality = new Cardinality($times, $times);

        return $this;
    }

    /**
     * Requires that the next verification matches a number of times greater
     * than or equal to $minimum.
     *
     * @param int $minimum The minimum match count.
     *
     * @return $this This verifier.
     */
    public function atLeast($minimum)
    {
        $this->cardinality = new Cardinality($minimum, null);

        return $this;
    }

    /**
     * Requires that the next verification matches a number of times less than
     * or equal to $maximum.
     *
     * @param int $maximum The maximum match count.
     *
     * @return $this This verifier.
     */
    public function atMost($maximum)
    {
        $this->cardinality = new Cardinality(0, $maximum);

        return $this;
    }

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
    public function between($minimum, $maximum)
    {
        $this->cardinality = new Cardinality($minimum, $maximum);

        return $this;
    }

    /**
     * Requires that the next verification matches for all possible items.
     *
     * @return $this This verifier.
     */
    public function always()
    {
        $this->cardinality->setIsAlways(true);

        return $this;
    }

    /**
     * Reset the cardinality to its default value.
     *
     * @return Cardinality The current cardinality.
     */
    public function resetCardinality()
    {
        $cardinality = $this->cardinality;
        $this->atLeast(1);

        return $cardinality;
    }

    /**
     * Get the cardinality.
     *
     * @return Cardinality The cardinality.
     */
    public function cardinality()
    {
        return $this->cardinality;
    }

    protected $cardinality;
}

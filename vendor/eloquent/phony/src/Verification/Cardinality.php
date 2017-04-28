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
use Eloquent\Phony\Verification\Exception\InvalidCardinalityStateException;
use Eloquent\Phony\Verification\Exception\InvalidSingularCardinalityException;

/**
 * Represents the cardinality of a verification.
 */
class Cardinality
{
    /**
     * Construct a new cardinality.
     *
     * @param int      $minimum  The minimum.
     * @param int|null $maximum  The maximum, or null for no maximum.
     * @param bool     $isAlways True if 'always' should be enabled.
     *
     * @throws InvalidCardinalityException If the cardinality is invalid.
     */
    public function __construct(
        $minimum = 1,
        $maximum = null,
        $isAlways = false
    ) {
        if ($minimum < 0 || $maximum < 0) {
            throw new InvalidCardinalityStateException();
        }

        if (null !== $maximum && $minimum > $maximum) {
            throw new InvalidCardinalityStateException();
        }

        if (null === $maximum && !$minimum) {
            throw new InvalidCardinalityStateException();
        }

        $this->minimum = $minimum;
        $this->maximum = $maximum;
        $this->setIsAlways($isAlways);
    }

    /**
     * Get the minimum.
     *
     * @return int The minimum.
     */
    public function minimum()
    {
        return $this->minimum;
    }

    /**
     * Get the maximum.
     *
     * @return int|null The maximum.
     */
    public function maximum()
    {
        return $this->maximum;
    }

    /**
     * Returns true if this cardinality is 'never'.
     *
     * @return bool True if this cardinality is 'never'.
     */
    public function isNever()
    {
        return 0 === $this->maximum;
    }

    /**
     * Turn 'always' on or off.
     *
     * @param  bool                        $isAlways True to enable 'always'.
     * @throws InvalidCardinalityException If the cardinality is invalid.
     */
    public function setIsAlways($isAlways)
    {
        if ($isAlways && $this->isNever()) {
            throw new InvalidCardinalityStateException();
        }

        $this->isAlways = $isAlways;
    }

    /**
     * Returns true if 'always' is enabled.
     *
     * @return bool True if 'always' is enabled.
     */
    public function isAlways()
    {
        return $this->isAlways;
    }

    /**
     * Returns true if the supplied count matches this cardinality.
     *
     * @param int|bool $count        The count or result to check.
     * @param int      $maximumCount The maximum possible count.
     *
     * @return bool True if the supplied count matches this cardinality.
     */
    public function matches($count, $maximumCount)
    {
        $count = intval($count);
        $result = true;

        if ($count < $this->minimum) {
            $result = false;
        }

        if (null !== $this->maximum && $count > $this->maximum) {
            $result = false;
        }

        if ($this->isAlways && $count < $maximumCount) {
            $result = false;
        }

        return $result;
    }

    /**
     * Asserts that this cardinality is suitable for events that can only happen
     * once or not at all.
     *
     * @return $this                       This cardinality.
     * @throws InvalidCardinalityException If the cardinality is invalid.
     */
    public function assertSingular()
    {
        if ($this->minimum > 1 || $this->maximum > 1 || $this->isAlways) {
            throw new InvalidSingularCardinalityException($this);
        }

        return $this;
    }

    private $minimum;
    private $maximum;
    private $isAlways;
}

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
use Eloquent\Phony\Mock\Handle\InstanceHandle;
use Eloquent\Phony\Mock\Mock;
use Eloquent\Phony\Spy\IterableSpy;
use Exception;
use Generator;
use Throwable;

/**
 * A matcher that tests if the value is strictly equal to (===) another
 * value. Arrays and objects are descended into, comparing each key/value
 * pair individually.
 */
class EqualToMatcher implements Matcher
{
    /**
     * Construct a new equal to matcher.
     *
     * @param mixed    $value           The value to check against.
     * @param bool     $useSubstitution True to use substitution for wrapper types.
     * @param Exporter $exporter        The exporter to use.
     */
    public function __construct($value, $useSubstitution, Exporter $exporter)
    {
        $this->value = $value;
        $this->useSubstitution = $useSubstitution;
        $this->exporter = $exporter;
    }

    /**
     * Get the value.
     *
     * @return mixed The value.
     */
    public function value()
    {
        return $this->value;
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
        $left = $this->value;
        $right = $value;

        if ($this->useSubstitution) {
            if ($left instanceof IterableSpy) {
                $left = $left->iterable();
            } elseif (
                $left instanceof Generator &&
                isset($left->_phonySubject)
            ) {
                $left = $left->_phonySubject;
            } elseif ($left instanceof InstanceHandle) {
                $left = $left->get();
            }

            if ($right instanceof IterableSpy) {
                $right = $right->iterable();
            } elseif (
                $right instanceof Generator &&
                isset($right->_phonySubject)
            ) {
                $right = $right->_phonySubject;
            } elseif ($right instanceof InstanceHandle) {
                $right = $right->get();
            }
        }

        /*
         * @var array<string,bool> The set of object comparisons that have been made.
         *
         * Keys are of the format "<left spl hash>:<right spl hash>"; values are
         * always TRUE. This set is used to detect comparisons that have already
         * been made in order to avoid infinite recursion when comparing cyclic
         * data structures.
         */
        $visitedObjects = array();

        /*
         * @var array<string,bool> The set of array comparisons that have been made.
         *
         * Keys are of the format "<left id>:<right id>"; values are always
         * TRUE. This set is used to detect comparisons that have already been
         * made in order to avoid infinite recursion when comparing cyclic data
         * structures.
         */
        $visitedArrays = array();

        /*
         * @var array<&array> Arrays that have been marked with an internal ID.
         *
         * In order to detect cyclic arrays we need to mark them with an ID.
         * This ID must be removed upon completion of the comparison.
         */
        $markedArrays = array();

        /*
         * @var array<&array> Stacks of values that are currently being compared.
         *
         * We maintain our own stack in order to:
         *
         *  - make fewer function calls (much faster in PHP 5.*)
         *  - avoid stack-depth limits
         *  - allow for 'continuations'
         *  - avoid unwinding when comparison fails
         *
         * Separate stacks are used for left/right to avoid construction of
         * temporary arrays.
         */
        $leftStack = array();
        $rightStack = array();

        /*
         * @var int The number of elements on the stacks.
         */
        $stackSize = 0;

        // ---------------------------------------------------------
        // This is the main entry point of the comparison algorithm.
        // ---------------------------------------------------------
        compare:

        if (is_array($left) && is_array($right)) {

            // Fetch or generate a unique ID for the left-hand-side.
            if (isset($left[self::ARRAY_ID_KEY])) {
                $leftId = $left[self::ARRAY_ID_KEY];
            } else {
                reset($left);
                $leftId = count($markedArrays) + 1;
                $left[self::ARRAY_ID_KEY] = $leftId;
                $markedArrays[] = &$left;
            }

            // Fetch or generate a unique ID for the right-hand-side.
            if (isset($right[self::ARRAY_ID_KEY])) {
                $rightId = $right[self::ARRAY_ID_KEY];
            } else {
                reset($right);
                $rightId = count($markedArrays) + 1;
                $right[self::ARRAY_ID_KEY] = $rightId;
                $markedArrays[] = &$right;
            }

            // Left and right are references to the same array.
            if ($leftId === $rightId) {
                goto pass;
            }

            $comparisonId = $leftId . ':' . $rightId;

            // These two arrays have already been compared.
            if (isset($visitedArrays[$comparisonId])) {
                goto pass;
            }

            // Record the comparison.
            $visitedArrays[$comparisonId] = true;

            // ---------------------------------
            // Compare the next array key/value.
            // ---------------------------------
            compareNextArrayElement:

            // Get the current key for each array, skipping our internal ID
            // value.
            $leftKey = key($left);
            next($left);

            if ($leftKey === self::ARRAY_ID_KEY) {
                $leftKey = key($left);
                next($left);
            }

            $rightKey = key($right);
            next($right);

            if ($rightKey === self::ARRAY_ID_KEY) {
                $rightKey = key($right);
                next($right);
            }

            // Keys can only be string|int (or null, if end of array).
            // Compare them using regular PHP comparison.
            if ($leftKey !== $rightKey) {
                return false;

            // Both keys are null, which means that both array are the same
            // length.
            } elseif (null === $leftKey) {
                goto pass;
            }

            // Push the arrays we're comparing on to the stack and start
            // comparing the values of this element.
            $leftStack[$stackSize] = &$left;
            $rightStack[$stackSize] = &$right;
            ++$stackSize;

            $left = &$left[$leftKey];
            $right = &$right[$rightKey];

            goto compare;
        }

        // Objects and other non-arrays can be compared with ===, as it will
        // not recurse in either case.
        if ($left === $right) {
            goto pass;
        }

        // Non-objects are not identical.
        if (!is_object($left) || !is_object($right)) {
            return false;
        }

        $leftClass = get_class($left);
        $rightClass = get_class($right);

        // The class names do not match.
        if ($leftClass !== $rightClass) {
            return false;
        }

        $comparisonId = spl_object_hash($left) . ':' . spl_object_hash($right);

        // These two objects have already been compared.
        if (isset($visitedObjects[$comparisonId])) {
            goto pass;
        }

        // Record the comparison.
        $visitedObjects[$comparisonId] = true;

        /*
         * Cast the objects as arrays and start comparing them.
         *
         * Importantly, the array cast operator maintains private and protected
         * properties, as well as arbitrary properties added to the object after
         * construction.
         *
         * Some special properties are removed for the purposes of comparison.
         *
         * @see https://github.com/php/php-src/commit/5721132
         */

        $leftIsMock = $left instanceof Mock;
        $leftIsException =
            $left instanceof Throwable || $left instanceof Exception;

        $left = (array) $left;
        unset($left["\0gcdata"]);

        if ($leftIsMock) {
            $handleProperty = "\0" . $leftClass . "\0_handle";

            if ($left[$handleProperty]) {
                $left['phony.label'] = $left[$handleProperty]->label();
            }

            unset($left[$handleProperty]);
        }

        if ($leftIsException) {
            unset(
                $left["\0*\0file"],
                $left["\0*\0line"],
                $left["\0Exception\0trace"],
                $left["\0Exception\0string"],
                $left['xdebug_message']
            );
        }

        $rightIsMock = $right instanceof Mock;
        $rightIsException =
            $right instanceof Throwable || $right instanceof Exception;

        $right = (array) $right;
        unset($right["\0gcdata"]);

        if ($rightIsMock) {
            $handleProperty = "\0" . $rightClass . "\0_handle";

            if ($right[$handleProperty]) {
                $right['phony.label'] = $right[$handleProperty]->label();
            }

            unset($right[$handleProperty]);
        }

        if ($rightIsException) {
            unset(
                $right["\0*\0file"],
                $right["\0*\0line"],
                $right["\0Exception\0trace"],
                $right["\0Exception\0string"],
                $right['xdebug_message']
            );
        }

        goto compareNextArrayElement;

        // -----------------------------
        // The current values are equal!
        // -----------------------------
        pass:

        // The stack is not empty, pop some values and compare again.
        if ($stackSize--) {
            $left = &$leftStack[$stackSize];
            $right = &$rightStack[$stackSize];

            goto compareNextArrayElement;
        }

        // Stack is empty, there's nothing left to compare. Clean up the
        // injected array IDs and return
        foreach ($markedArrays as &$array) {
            unset($array[self::ARRAY_ID_KEY]);
        }

        return true;
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
        if ($exporter) {
            return $exporter->export($this->value);
        }

        return $this->exporter->export($this->value);
    }

    /**
     * Describe this matcher.
     *
     * @return string The description.
     */
    public function __toString()
    {
        return $this->exporter->export($this->value);
    }

    const ARRAY_ID_KEY = "\0__phony__\0";

    private $value;
    private $useSubstitution;
    private $exporter;
}

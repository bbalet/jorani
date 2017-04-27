<?php

/*
 * This file is part of the Phony package.
 *
 * Copyright © 2016 Erin Millard
 *
 * For the full copyright and license information, please view the LICENSE file
 * that was distributed with this source code.
 */

namespace Eloquent\Phony\Assertion\Exception;

use Exception;
use ReflectionClass;

/**
 * Represents a failed assertion.
 */
final class AssertionException extends Exception
{
    /**
     * Trim the supplied exception's stack trace to only include relevant
     * information.
     *
     * Also replaces the file path and line number.
     *
     * @param Exception $exception The exception.
     */
    public static function trim(Exception $exception)
    {
        $reflector = new ReflectionClass('Exception');

        $traceProperty = $reflector->getProperty('trace');
        $traceProperty->setAccessible(true);
        $fileProperty = $reflector->getProperty('file');
        $fileProperty->setAccessible(true);
        $lineProperty = $reflector->getProperty('line');
        $lineProperty->setAccessible(true);

        $call = static::tracePhonyCall($traceProperty->getValue($exception));

        if ($call) {
            $traceProperty->setValue($exception, array($call));
            $fileProperty->setValue(
                $exception,
                isset($call['file']) ? $call['file'] : null
            );
            $lineProperty->setValue(
                $exception,
                isset($call['line']) ? $call['line'] : null
            );
        } else {
            $traceProperty->setValue($exception, array());
            $fileProperty->setValue($exception, null);
            $lineProperty->setValue($exception, null);
        }
    }

    /**
     * Find the Phony entry point call in a stack trace.
     *
     * @param array $trace The stack trace.
     *
     * @return array|null The call, or null if unable to determine the entry point.
     */
    public static function tracePhonyCall(array $trace)
    {
        $prefix = 'Eloquent\Phony\\';

        for ($i = count($trace) - 1; $i >= 0; --$i) {
            $entry = $trace[$i];

            if (isset($entry['class'])) {
                if (0 === strpos($entry['class'], $prefix)) {
                    return $entry;
                }
            } elseif (0 === strpos($entry['function'], $prefix)) {
                return $entry;
            }
        }

        return null;
    }

    /**
     * Construct a new assertion exception.
     *
     * @param string $description The failure description.
     */
    public function __construct($description)
    {
        parent::__construct($description);

        static::trim($this);
    }
}

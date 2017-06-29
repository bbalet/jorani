<?php

/*
 * This file is part of the Phony package.
 *
 * Copyright © 2016 Erin Millard
 *
 * For the full copyright and license information, please view the LICENSE file
 * that was distributed with this source code.
 */

namespace Eloquent\Phony\Hook\Exception;

use Exception;

/**
 * The function hook has a different signature to the supplied callback.
 */
final class FunctionSignatureMismatchException extends Exception implements
    FunctionHookException
{
    /**
     * Construct a function signature mismatch exception.
     *
     * @param string $functionName The function name.
     */
    public function __construct($functionName)
    {
        $this->functionName = $functionName;

        parent::__construct(
            sprintf(
                'Function %s has a different signature to the supplied ' .
                    'callback.',
                var_export($functionName, true)
            )
        );
    }

    /**
     * Get the function name.
     *
     * @return string The function name.
     */
    public function functionName()
    {
        return $this->functionName;
    }

    private $functionName;
}

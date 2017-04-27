<?php

/*
 * This file is part of the Phony package.
 *
 * Copyright © 2016 Erin Millard
 *
 * For the full copyright and license information, please view the LICENSE file
 * that was distributed with this source code.
 */

namespace Eloquent\Phony\Mock\Exception;

use Exception;

/**
 * Unable to extend final class.
 */
final class FinalClassException extends Exception implements
    MockException
{
    /**
     * Construct a final class exception.
     *
     * @param string $className The class name.
     */
    public function __construct($className)
    {
        $this->className = $className;

        parent::__construct(
            sprintf(
                'Unable to extend final class %s.',
                var_export($className, true)
            )
        );
    }

    /**
     * Get the class name.
     *
     * @return string The class name.
     */
    public function className()
    {
        return $this->className;
    }

    private $className;
}

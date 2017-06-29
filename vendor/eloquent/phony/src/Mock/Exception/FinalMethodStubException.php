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
 * The method cannot be stubbed because it is final.
 */
final class FinalMethodStubException extends Exception implements
    MockException
{
    /**
     * Construct a new final method stub exception.
     *
     * @param string $className The class name.
     * @param string $name      The method name.
     */
    public function __construct($className, $name)
    {
        $this->className = $className;
        $this->name = $name;

        parent::__construct(
            sprintf(
                'The method %s::%s() cannot be stubbed because it is final.',
                $className,
                $name
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

    /**
     * Get the method name.
     *
     * @return string The method name.
     */
    public function name()
    {
        return $this->name;
    }

    private $className;
    private $name;
}

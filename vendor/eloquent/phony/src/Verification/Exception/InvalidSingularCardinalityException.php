<?php

/*
 * This file is part of the Phony package.
 *
 * Copyright © 2016 Erin Millard
 *
 * For the full copyright and license information, please view the LICENSE file
 * that was distributed with this source code.
 */

namespace Eloquent\Phony\Verification\Exception;

use Exception;

/**
 * The specified cardinality is invalid for events that can only happen once or
 * not at all.
 */
final class InvalidSingularCardinalityException extends Exception implements
    InvalidCardinalityException
{
    /**
     * Construct a new invalid singular cardinality exception.
     *
     * @param tuple<int,int|null> $cardinality The cardinality.
     */
    public function __construct($cardinality)
    {
        $this->cardinality = $cardinality;

        parent::__construct(
            'The specified cardinality is invalid for events ' .
                'that can only happen once or not at all.'
        );
    }

    /**
     * Get the cardinality.
     *
     * @return tuple<int,int|null> The cardinality.
     */
    public function cardinality()
    {
        return $this->cardinality;
    }

    private $cardinality;
}

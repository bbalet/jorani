<?php

/*
 * This file is part of the Phony package.
 *
 * Copyright © 2016 Erin Millard
 *
 * For the full copyright and license information, please view the LICENSE file
 * that was distributed with this source code.
 */

namespace Eloquent\Phony\Reflection\Exception;

use Exception;

/**
 * The specified feature is undefined.
 */
final class UndefinedFeatureException extends Exception
{
    /**
     * Construct a new undefined feature exception.
     *
     * @param string $feature The feature.
     */
    public function __construct($feature)
    {
        $this->feature = $feature;

        parent::__construct(
            sprintf('Undefined feature %s.', var_export($feature, true))
        );
    }

    /**
     * Get the feature.
     *
     * @return string The feature.
     */
    public function feature()
    {
        return $this->feature;
    }

    private $feature;
}

<?php

/*
 * This file is part of the Phony package.
 *
 * Copyright © 2016 Erin Millard
 *
 * For the full copyright and license information, please view the LICENSE file
 * that was distributed with this source code.
 */

namespace Eloquent\Phony\Stub\Exception;

use Eloquent\Phony\Assertion\AssertionRenderer;
use Eloquent\Phony\Matcher\Matchable;
use Exception;

/**
 * Stub criteria were specified, but never used.
 */
final class UnusedStubCriteriaException extends Exception
{
    /**
     * Construct a new unused stub criteria exception.
     *
     * @param array<Matchable> $criteria The criteria.
     */
    public function __construct(array $criteria)
    {
        $this->criteria = $criteria;

        parent::__construct(
            sprintf(
                'Stub criteria %s were never used. ' .
                    'Check for incomplete stub rules.',
                var_export(
                    AssertionRenderer::instance()->renderMatchers($criteria),
                    true
                )
            )
        );
    }

    /**
     * Get the criteria.
     *
     * @return array<Matchable> The criteria.
     */
    public function criteria()
    {
        return $this->criteria;
    }

    private $criteria;
}

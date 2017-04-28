<?php

/*
 * This file is part of the Phony package.
 *
 * Copyright © 2016 Erin Millard
 *
 * For the full copyright and license information, please view the LICENSE file
 * that was distributed with this source code.
 */

namespace Eloquent\Phony\Mock\Builder;

use Eloquent\Phony\Invocation\InvocableInspector;
use Eloquent\Phony\Mock\Handle\HandleFactory;
use Eloquent\Phony\Mock\MockFactory;
use Eloquent\Phony\Reflection\FeatureDetector;

/**
 * Creates mock builders.
 */
class MockBuilderFactory
{
    /**
     * Get the static instance of this factory.
     *
     * @return MockBuilderFactory The static factory.
     */
    public static function instance()
    {
        if (!self::$instance) {
            self::$instance = new self(
                MockFactory::instance(),
                HandleFactory::instance(),
                InvocableInspector::instance(),
                FeatureDetector::instance()
            );
        }

        return self::$instance;
    }

    /**
     * Construct a new mock builder factory.
     *
     * @param MockFactory        $mockFactory        The mock factory to use.
     * @param HandleFactory      $handleFactory      The handle factory to use.
     * @param InvocableInspector $invocableInspector The invocable inspector.
     * @param FeatureDetector    $featureDetector    The feature detector to use.
     */
    public function __construct(
        MockFactory $mockFactory,
        HandleFactory $handleFactory,
        InvocableInspector $invocableInspector,
        FeatureDetector $featureDetector
    ) {
        $this->mockFactory = $mockFactory;
        $this->handleFactory = $handleFactory;
        $this->invocableInspector = $invocableInspector;
        $this->featureDetector = $featureDetector;
    }

    /**
     * Create a new mock builder.
     *
     * Each value in `$types` can be either a class name, or an ad hoc mock
     * definition. If only a single type is being mocked, the class name or
     * definition can be passed without being wrapped in an array.
     *
     * @param mixed $types The types to mock.
     *
     * @return MockBuilder The mock builder.
     */
    public function create($types = array())
    {
        return new MockBuilder(
            $types,
            $this->mockFactory,
            $this->handleFactory,
            $this->invocableInspector,
            $this->featureDetector
        );
    }

    private static $instance;
    private $mockFactory;
    private $handleFactory;
    private $invocableInspector;
    private $featureDetector;
}

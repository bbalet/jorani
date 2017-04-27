<?php

/*
 * This file is part of the Phony package.
 *
 * Copyright © 2016 Erin Millard
 *
 * For the full copyright and license information, please view the LICENSE file
 * that was distributed with this source code.
 */

namespace Eloquent\Phony\Stub\Answer\Builder;

use Eloquent\Phony\Invocation\InvocableInspector;
use Eloquent\Phony\Invocation\Invoker;
use Eloquent\Phony\Reflection\FeatureDetector;
use Eloquent\Phony\Stub\Stub;

/**
 * Creates generator answer builders.
 */
class GeneratorAnswerBuilderFactory
{
    /**
     * Get the static instance of this factory.
     *
     * @return GeneratorAnswerBuilderFactory The static factory.
     */
    public static function instance()
    {
        if (!self::$instance) {
            self::$instance = new self(
                InvocableInspector::instance(),
                Invoker::instance(),
                FeatureDetector::instance()
            );
        }

        return self::$instance;
    }

    /**
     * Construct a new generator answer builder factory.
     *
     * @param InvocableInspector $invocableInspector The invocable inspector to use.
     * @param Invoker            $invoker            The invoker to use.
     * @param FeatureDetector    $featureDetector    The feature detector to use.
     */
    public function __construct(
        InvocableInspector $invocableInspector,
        Invoker $invoker,
        FeatureDetector $featureDetector
    ) {
        $this->invocableInspector = $invocableInspector;
        $this->invoker = $invoker;

        $this->isGeneratorReturnSupported =
            $featureDetector->isSupported('generator.return');
    }

    /**
     * Create a generator answer builder for the supplied stub.
     *
     * @param Stub $stub The stub.
     *
     * @return GeneratorAnswerBuilder The newly created builder.
     */
    public function create(Stub $stub)
    {
        return new GeneratorAnswerBuilder(
            $stub,
            $this->isGeneratorReturnSupported,
            $this->invocableInspector,
            $this->invoker
        );
    }

    private static $instance;
    private $invocableInspector;
    private $invoker;
    private $isGeneratorReturnSupported;
}

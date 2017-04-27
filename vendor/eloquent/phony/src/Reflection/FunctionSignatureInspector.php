<?php

/*
 * This file is part of the Phony package.
 *
 * Copyright © 2016 Erin Millard
 *
 * For the full copyright and license information, please view the LICENSE file
 * that was distributed with this source code.
 */

namespace Eloquent\Phony\Reflection;

use Eloquent\Phony\Invocation\InvocableInspector;
use ReflectionFunctionAbstract;

/**
 * An abstract base class for implementing function signature inspectors.
 */
abstract class FunctionSignatureInspector
{
    /**
     * Get the static instance of this inspector.
     *
     * @return FunctionSignatureInspector The static inspector.
     */
    public static function instance()
    {
        if (!self::$instance) {
            $featureDetector = FeatureDetector::instance();

            if ($featureDetector->isSupported('runtime.hhvm')) {
                // @codeCoverageIgnoreStart
                self::$instance = new HhvmFunctionSignatureInspector(
                    InvocableInspector::instance(),
                    $featureDetector
                );
                // @codeCoverageIgnoreEnd
            } else {
                self::$instance = new PhpFunctionSignatureInspector(
                    InvocableInspector::instance(),
                    $featureDetector
                );
            }
        }

        return self::$instance;
    }

    /**
     * Construct a new function signature inspector.
     *
     * @param InvocableInspector $invocableInspector The invocable inspector to use.
     */
    public function __construct(InvocableInspector $invocableInspector)
    {
        $this->invocableInspector = $invocableInspector;
    }

    /**
     * Get the function signature of the supplied callback.
     *
     * @param callable $callback The callback.
     *
     * @return array<string,array<string>> The callback signature.
     */
    public function callbackSignature($callback)
    {
        return $this->signature(
            $this->invocableInspector->callbackReflector($callback)
        );
    }

    /**
     * Get the function signature of the supplied function.
     *
     * @param ReflectionFunctionAbstract $function The function.
     *
     * @return array<string,array<string>> The function signature.
     */
    abstract public function signature(ReflectionFunctionAbstract $function);

    private static $instance;
    private $invocableInspector;
}

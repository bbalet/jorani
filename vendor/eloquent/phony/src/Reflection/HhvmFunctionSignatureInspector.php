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
 * Inspects functions to determine their signature under HHVM.
 *
 * @codeCoverageIgnore
 */
class HhvmFunctionSignatureInspector extends FunctionSignatureInspector
{
    /**
     * Construct a new function signature inspector.
     *
     * @param InvocableInspector $invocableInspector The invocable inspector to use.
     * @param FeatureDetector    $featureDetector    The feature detector to use.
     */
    public function __construct(
        InvocableInspector $invocableInspector,
        FeatureDetector $featureDetector
    ) {
        parent::__construct($invocableInspector);

        $this->isVariadicParameterSupported = $featureDetector
            ->isSupported('parameter.variadic');
        $this->isIterableTypeHintSupported = $featureDetector
            ->isSupported('type.iterable');
    }

    /**
     * Get the function signature of the supplied function.
     *
     * @param ReflectionFunctionAbstract $function The function.
     *
     * @return array<string,array<string>> The function signature.
     */
    public function signature(ReflectionFunctionAbstract $function)
    {
        $signature = array();

        foreach ($function->getParameters() as $parameter) {
            $name = $parameter->getName();

            if ($typehint = $parameter->getTypehintText()) {
                switch ($typehint) {
                    case 'array':
                    case 'callable':
                        $typehint .= ' ';

                        break;

                    case 'iterable':
                        if ($this->isIterableTypeHintSupported) {
                            $typehint .= ' ';

                            break;
                        }

                        // fall through to default behavior

                    default:
                        $typehint = '\\' . $typehint . ' ';
                }
            }

            $byReference = $parameter->isPassedByReference() ? '&' : '';

            if (
                $this->isVariadicParameterSupported &&
                $parameter->isVariadic()
            ) {
                $variadic = '...';
            } else {
                $variadic = '';
            }

            $defaultValue = $parameter->getDefaultValueText();

            if ('' !== $defaultValue) {
                if ('NULL' === $defaultValue) {
                    $defaultValue = ' = null';
                } elseif (
                    'PHP_INT_MAX' === $defaultValue ||
                    'PHP_INT_MIN' === $defaultValue
                ) {
                    $typehint = '';
                    $defaultValue = ' = ' . $defaultValue;
                } else {
                    $defaultValue = eval('return ' . $defaultValue . ';');

                    if ('\HH\float ' === $typehint) {
                        $defaultValue = sprintf(' = %f', $defaultValue);
                    } else {
                        $defaultValue = ' = ' . var_export($defaultValue, true);
                    }
                }
            }

            $signature[$name] =
                array($typehint, $byReference, $variadic, $defaultValue);
        }

        return $signature;
    }

    private $isVariadicParameterSupported;
    private $isIterableTypeHintSupported;
}

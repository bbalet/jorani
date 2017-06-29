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
 * Inspects functions to determine their signature under PHP.
 */
class PhpFunctionSignatureInspector extends FunctionSignatureInspector
{
    const PARAMETER_PATTERN = '/^\s*Parameter #\d+ \[ <(required|optional)> (\S+ )?(or NULL )?(&)?(?:\.\.\.)?\$(\S+)( = [^$]+)? ]$/m';

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

        $this->isExportReferenceSupported = $featureDetector
            ->isSupported('reflection.function.export.reference');
        $this->isVariadicParameterSupported = $featureDetector
            ->isSupported('parameter.variadic');
        $this->isScalarTypeHintSupported = $featureDetector
            ->isSupported('parameter.hint.scalar');
        $this->isCallableTypeHintSupported = $featureDetector
            ->isSupported('type.callable');
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
        $isMatch = preg_match_all(
            static::PARAMETER_PATTERN,
            $function,
            $matches,
            PREG_SET_ORDER
        );

        if (!$isMatch) {
            return array();
        }

        $parameters = $function->getParameters();
        $signature = array();
        $index = -1;

        foreach ($matches as $match) {
            $parameter = $parameters[++$index];

            $typehint = $match[2];

            if ('self ' === $typehint) {
                $typehint = '\\' . $parameter->getDeclaringClass()->getName()
                    . ' ';
            } elseif (
                '' !== $typehint &&
                'array ' !== $typehint &&
                (
                    !$this->isCallableTypeHintSupported ||
                    'callable ' !== $typehint
                ) &&
                (
                    !$this->isIterableTypeHintSupported ||
                    'iterable ' !== $typehint
                )
            ) {
                if (!$this->isScalarTypeHintSupported) {
                    $typehint = '\\' . $typehint; // @codeCoverageIgnore
                } elseif (
                    'integer ' === $typehint &&
                    $parameter->getType()->isBuiltin()
                ) {
                    $typehint = 'int ';
                } elseif (
                    'boolean ' === $typehint &&
                    $parameter->getType()->isBuiltin()
                ) {
                    $typehint = 'bool ';
                } elseif ('float ' !== $typehint && 'string ' !== $typehint) {
                    $typehint = '\\' . $typehint;
                }
            }

            if ($this->isExportReferenceSupported) {
                $byReference = $match[4];
            } else {
                $byReference = $parameter->isPassedByReference() ? '&' : ''; // @codeCoverageIgnore
            }

            if (
                $this->isVariadicParameterSupported &&
                $parameter->isVariadic()
            ) {
                $variadic = '...';
                $optional = false;
            } else {
                $variadic = '';
                $optional = 'optional' === $match[1];
            }

            if (isset($match[6])) {
                if (' = NULL' === $match[6]) {
                    $defaultValue = ' = null';
                } else {
                    $defaultValue = ' = ' .
                        var_export($parameter->getDefaultValue(), true);
                }
            } elseif ($optional || $match[3]) {
                $defaultValue = ' = null';
            } else {
                $defaultValue = '';
            }

            $signature[$match[5]] =
                array($typehint, $byReference, $variadic, $defaultValue);
        }

        return $signature;
    }

    private $isExportReferenceSupported;
    private $isVariadicParameterSupported;
    private $isScalarTypeHintSupported;
    private $isCallableTypeHintSupported;
    private $isIterableTypeHintSupported;
}

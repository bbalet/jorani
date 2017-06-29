<?php

/*
 * This file is part of the Phony package.
 *
 * Copyright © 2016 Erin Millard
 *
 * For the full copyright and license information, please view the LICENSE file
 * that was distributed with this source code.
 */

namespace Eloquent\Phony\Stub;

use Eloquent\Phony\Mock\Builder\MockBuilderFactory;
use Eloquent\Phony\Reflection\FeatureDetector;
use ReflectionFunctionAbstract;
use ReflectionType;

/**
 * Creates empty values from arbitrary types.
 */
class EmptyValueFactory
{
    /**
     * Get the static instance of this factory.
     *
     * @return EmptyValueFactory The static factory.
     */
    public static function instance()
    {
        if (!self::$instance) {
            self::$instance = new self(FeatureDetector::instance());
        }

        return self::$instance;
    }

    /**
     * Construct a new empty value factory.
     *
     * @param FeatureDetector $featureDetector The feature detector to use.
     */
    public function __construct(FeatureDetector $featureDetector)
    {
        $this->isReturnTypeSupported =
            $featureDetector->isSupported('return.type');
    }

    /**
     * Set the stub verifier factory.
     *
     * @param StubVerifierFactory $stubVerifierFactory The stub verifier factory to use.
     */
    public function setStubVerifierFactory(
        StubVerifierFactory $stubVerifierFactory
    ) {
        $this->stubVerifierFactory = $stubVerifierFactory;
    }

    /**
     * Set the mock builder factory.
     *
     * @param MockBuilderFactory $mockBuilderFactory The mock builder factory to use.
     */
    public function setMockBuilderFactory(
        MockBuilderFactory $mockBuilderFactory
    ) {
        $this->mockBuilderFactory = $mockBuilderFactory;
    }

    /**
     * Create a value of the supplied type.
     *
     * @param ReflectionType $type The type.
     *
     * @return mixed A value of the supplied type.
     */
    public function fromType(ReflectionType $type)
    {
        if ($type->allowsNull()) {
            return null;
        }

        $typeName = strval($type);

        switch (strtolower($typeName)) {
            case 'bool':
            case 'hh\bool':
                return false;

            case 'int':
            case 'hh\int':
                return 0;

            case 'float':
            case 'hh\float':
                return .0;

            case 'string':
            case 'hh\string':
                return '';

            case 'array':
            case 'iterable':
                return array();

            case 'stdclass':
                return (object) array();

            case 'callable':
                return $this->stubVerifierFactory->create();

            case 'closure':
                return function () {};

            case 'generator':
                $fn = function () { return; yield; };

                return $fn();

            // @codeCoverageIgnoreStart

            case 'void':
            case 'hh\mixed':
                return null;
        }
        // @codeCoverageIgnoreEnd

        return $this->mockBuilderFactory->create($typeName)->full();
    }

    /**
     * Create a return value for the supplied function.
     *
     * @param ReflectionFunctionAbstract $function The function.
     *
     * @return mixed A value that can be returned by the function.
     */
    public function fromFunction(ReflectionFunctionAbstract $function)
    {
        if (!$this->isReturnTypeSupported) {
            return null; // @codeCoverageIgnore
        }

        if ($type = $function->getReturnType()) {
            return $this->fromType($type);
        }

        return null;
    }

    private static $instance;
    private $stubVerifierFactory;
    private $mockBuilderFactory;
    private $isReturnTypeSupported;
}

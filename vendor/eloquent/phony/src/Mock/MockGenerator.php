<?php

/*
 * This file is part of the Phony package.
 *
 * Copyright © 2016 Erin Millard
 *
 * For the full copyright and license information, please view the LICENSE file
 * that was distributed with this source code.
 */

namespace Eloquent\Phony\Mock;

use Eloquent\Phony\Mock\Builder\Method\TraitMethodDefinition;
use Eloquent\Phony\Mock\Builder\MockDefinition;
use Eloquent\Phony\Reflection\FeatureDetector;
use Eloquent\Phony\Reflection\FunctionSignatureInspector;
use Eloquent\Phony\Sequencer\Sequencer;

/**
 * Generates mock classes.
 */
class MockGenerator
{
    /**
     * Get the static instance of this generator.
     *
     * @return MockGenerator The static generator.
     */
    public static function instance()
    {
        if (!self::$instance) {
            self::$instance = new self(
                Sequencer::sequence('mock-class-label'),
                FunctionSignatureInspector::instance(),
                FeatureDetector::instance()
            );
        }

        return self::$instance;
    }

    /**
     * Construct a new mock generator.
     *
     * @param Sequencer                  $labelSequencer     The label sequencer to use.
     * @param FunctionSignatureInspector $signatureInspector The function signature inspector to use.
     * @param FeatureDetector            $featureDetector    The feature detector to use.
     */
    public function __construct(
        Sequencer $labelSequencer,
        FunctionSignatureInspector $signatureInspector,
        FeatureDetector $featureDetector
    ) {
        $this->labelSequencer = $labelSequencer;
        $this->signatureInspector = $signatureInspector;
        $this->featureDetector = $featureDetector;

        $this->isClosureBindingSupported =
            $this->featureDetector->isSupported('closure.bind');
        $this->isReturnTypeSupported =
            $this->featureDetector->isSupported('return.type');
        $this->isNullableTypeSupported =
            $this->featureDetector->isSupported('type.nullable');
        $this->isHhvm = $featureDetector->isSupported('runtime.hhvm');

        $this->canMockPharDestruct =
            $this->isHhvm || !version_compare(PHP_VERSION, '7.x', '<');
    }

    /**
     * Generate a mock class name.
     *
     * @param MockDefinition $definition The definition.
     *
     * @return string The mock class name.
     */
    public function generateClassName(MockDefinition $definition)
    {
        $className = $definition->className();

        if (null !== $className) {
            return $className;
        }

        $className = 'PhonyMock';
        $parentClassName = $definition->parentClassName();

        if (null !== $parentClassName) {
            $subject = $parentClassName;
        } elseif ($interfaceNames = $definition->interfaceNames()) {
            $subject = $interfaceNames[0];
        } elseif ($traitNames = $definition->traitNames()) {
            $subject = $traitNames[0];
        } else {
            $subject = null;
        }

        if (null !== $subject) {
            $subjectAtoms = preg_split('/[_\\\\]/', $subject);
            $className .= '_' . array_pop($subjectAtoms);
        }

        $className .= '_' . $this->labelSequencer->next();

        return $className;
    }

    /**
     * Generate a mock class and return the source code.
     *
     * @param MockDefinition $definition The definition.
     * @param string|null    $className  The class name.
     *
     * @return string The source code.
     */
    public function generate(
        MockDefinition $definition,
        $className = null
    ) {
        if (null === $className) {
            $className = $this->generateClassName($definition);
        }

        $source = $this->generateHeader($definition, $className) .
            $this->generateConstants($definition) .
            $this->generateMethods(
                $definition->methods()->publicStaticMethods()
            ) .
            $this->generateMagicCallStatic($definition) .
            $this->generateStructors($definition) .
            $this->generateMethods($definition->methods()->publicMethods()) .
            $this->generateMagicCall($definition) .
            $this->generateMethods(
                $definition->methods()->protectedStaticMethods()
            ) .
            $this->generateMethods($definition->methods()->protectedMethods()) .
            $this->generateCallParentMethods($definition) .
            $this->generateProperties($definition) .
            "\n}\n";

        // @codeCoverageIgnoreStart
        if (PHP_EOL !== "\n") {
            $source = str_replace("\n", PHP_EOL, $source);
        }
        // @codeCoverageIgnoreEnd

        return $source;
    }

    private function generateHeader($definition, $className)
    {
        $classNameParts = explode('\\', $className);

        if (count($classNameParts) > 1) {
            $className = array_pop($classNameParts);
            $namespace =
                'namespace ' . implode('\\', $classNameParts) . ";\n\n";
        } else {
            $namespace = '';
        }

        $source = $namespace . 'class ' . $className;

        $parentClassName = $definition->parentClassName();
        $interfaceNames = $definition->interfaceNames();
        $traitNames = $definition->traitNames();

        if (null !== $parentClassName) {
            $source .= "\nextends \\" . $parentClassName;
        }

        array_unshift($interfaceNames, 'Eloquent\Phony\Mock\Mock');
        $source .= "\nimplements \\" .
            implode(",\n           \\", $interfaceNames);

        $source .= "\n{";

        if ($traitNames) {
            $traitName = array_shift($traitNames);
            $source .= "\n    use \\" . $traitName;

            foreach ($traitNames as $traitName) {
                $source .= ",\n        \\" . $traitName;
            }

            $source .= "\n    {";

            $methods = $definition->methods();

            foreach ($methods->traitMethods() as $method) {
                $typeName = $method->method()->getDeclaringClass()->getName();
                $methodName = $method->name();

                $source .= "\n        \\" .
                    $typeName .
                    '::' .
                    $methodName .
                    "\n            as private _callTrait_" .
                    str_replace('\\', "\xc2\xa6", $typeName) .
                    "\xc2\xbb" .
                    $methodName .
                    ';';
            }

            $source .= "\n    }\n";
        }

        return $source;
    }

    private function generateConstants($definition)
    {
        $constants = $definition->customConstants();
        $source = '';

        if ($constants) {
            foreach ($constants as $name => $value) {
                $source .= "\n    const " .
                    $name .
                    ' = ' .
                    (null === $value ? 'null' : var_export($value, true)) .
                    ';';
            }

            $source .= "\n";
        }

        return $source;
    }

    private function generateMagicCallStatic($definition)
    {
        $methods = $definition->methods();
        $callStaticName = $methods->methodName('__callstatic');
        $methods = $methods->publicStaticMethods();

        if (!$callStaticName) {
            return '';
        }

        $methodReflector = $methods[$callStaticName]->method();
        $returnsReference = $methodReflector->returnsReference() ? '&' : '';

        $source = <<<EOD

    public static function ${returnsReference}__callStatic(
EOD;

        $signature = $this->signatureInspector
            ->signature($methodReflector);
        $index = -1;

        foreach ($signature as $parameter) {
            if (-1 !== $index) {
                $source .= ',';
            }

            $source .= "\n        " .
                $parameter[0] .
                $parameter[1] .
                '$a' .
                ++$index .
                $parameter[3];
        }

        if (
            $this->isReturnTypeSupported &&
            $methodReflector->hasReturnType()
        ) {
            $type = $methodReflector->getReturnType();
            $isBuiltin = $type->isBuiltin();

            if ($this->isHhvm) {
                // @codeCoverageIgnoreStart
                $typeString = $methodReflector->getReturnTypeText();

                if (0 === strpos($typeString, '?')) {
                    $typeString = '';
                } else {
                    $genericPosition = strpos($typeString, '<');

                    if (false !== $genericPosition) {
                        $typeString = substr($typeString, 0, $genericPosition);
                    }
                }

                $isBuiltin = $isBuiltin && false === strpos($typeString, '\\');
                // @codeCoverageIgnoreEnd
            } else {
                if ($type->allowsNull()) {
                    $typeString = '?' . $type;
                } else {
                    $typeString = (string) $type;
                }
            }

            if ('self' === $typeString) {
                $typeString = $methodReflector->getDeclaringClass()->getName();
            }

            if ($isBuiltin) {
                $source .= "\n    ) : " . $typeString . " {\n";
            } elseif (
                $this->isNullableTypeSupported &&
                0 === strpos($typeString, '?')
            ) {
                $source .= "\n    ) : ?\\" . substr($typeString, 1) . " {\n";
            } else {
                $source .= "\n    ) : \\" . $typeString . " {\n";
            }

            $isVoidReturn = $isBuiltin && 'void' === $typeString;
        } else {
            $source .= "\n    ) {\n";
            $isVoidReturn = false;
        }

        if ($isVoidReturn) {
            $source .= <<<'EOD'
        self::$_staticHandle->spy($a0)
            ->invokeWith(new \Eloquent\Phony\Call\Arguments($a1));
    }

EOD;
        } else {
            $source .= <<<'EOD'
        $result = self::$_staticHandle->spy($a0)
            ->invokeWith(new \Eloquent\Phony\Call\Arguments($a1));

        return $result;
    }

EOD;
        }

        return $source;
    }

    private function generateStructors($definition)
    {
        $constructor = null;
        $destructor = null;

        foreach ($definition->types() as $name => $type) {
            if (!$constructor) {
                $constructor = $type->getConstructor();

                if ($constructor && $constructor->isFinal()) {
                    return '';
                }
            }

            if (!$destructor && $type->hasMethod('__destruct')) {
                switch ($name) {
                    case 'phar':
                    case 'phardata':
                    case 'pharfileinfo':
                        if ($this->canMockPharDestruct) {
                            $destructor = $type->getMethod('__destruct');
                        }

                        break;

                    default:
                        $destructor = $type->getMethod('__destruct');
                }
            }
        }

        $source = '';

        if ($constructor) {
            $source .= <<<'EOD'

    public function __construct()
    {
    }

EOD;
        }

        if ($destructor) {
            $source .= <<<'EOD'

    public function __destruct()
    {
        if (!$this->_handle) {
            parent::__destruct();

            return;
        }

        $this->_handle->spy('__destruct')->invokeWith(array());
    }

EOD;
        }

        return $source;
    }

    private function generateMethods($methods)
    {
        $source = '';

        foreach ($methods as $method) {
            $name = $method->name();
            $nameLower = strtolower($name);
            $nameExported = var_export($name, true);
            $methodReflector = $method->method();

            switch ($nameLower) {
                case '__construct':
                case '__destruct':
                case '__call':
                case '__callstatic':
                    continue 2;
            }

            $signature = $this->signatureInspector->signature($methodReflector);

            if ($method->isCustom()) {
                $parameterName = null;

                foreach ($signature as $parameterName => $parameter) {
                    break;
                }

                if ('phonySelf' === $parameterName) {
                    array_shift($signature);
                }
            }

            $parameterCount = count($signature);
            $variadicIndex = -1;
            $variadicReference = '';

            if (empty($signature)) {
                $argumentPacking = '';
            } else {
                $argumentPacking = "\n";
                $index = -1;

                foreach ($signature as $parameter) {
                    if ($parameter[2]) {
                        --$parameterCount;

                        $variadicIndex = ++$index;
                        $variadicReference = $parameter[1];
                    } else {
                        $argumentPacking .=
                            "\n        if (\$argumentCount > " .
                            ++$index .
                            ") {\n            \$arguments[] = " .
                            $parameter[1] .
                            '$a' .
                            $index .
                            ";\n        }";
                    }
                }
            }

            if (
                $this->isReturnTypeSupported &&
                $methodReflector->hasReturnType()
            ) {
                $type = $methodReflector->getReturnType();
                $isBuiltin = $type->isBuiltin();

                if ($this->isHhvm) {
                    // @codeCoverageIgnoreStart
                    $typeString = $methodReflector->getReturnTypeText();

                    if (0 === strpos($typeString, '?')) {
                        $typeString = '';
                    } else {
                        $genericPosition = strpos($typeString, '<');

                        if (false !== $genericPosition) {
                            $typeString =
                                substr($typeString, 0, $genericPosition);
                        }
                    }

                    $isBuiltin =
                        $isBuiltin && false === strpos($typeString, '\\');
                    // @codeCoverageIgnoreEnd
                } else {
                    if ($type->allowsNull()) {
                        $typeString = '?' . $type;
                    } else {
                        $typeString = (string) $type;
                    }
                }

                if ('self' === $typeString) {
                    $typeString =
                        $methodReflector->getDeclaringClass()->getName();
                }

                if ($isBuiltin) {
                    $returnType = ' : ' . $typeString;
                } elseif (
                    $this->isNullableTypeSupported &&
                    0 === strpos($typeString, '?')
                ) {
                    $returnType = ' : ?\\' . substr($typeString, 1);
                } else {
                    $returnType = ' : \\' . $typeString;
                }

                $isVoidReturn = $isBuiltin && 'void' === $typeString;
            } else {
                $returnType = '';
                $isVoidReturn = false;
            }

            $isStatic = $method->isStatic() ? 'static ' : '';

            if ($isStatic) {
                $handle = 'self::$_staticHandle';
            } else {
                $handle = '$this->_handle';
            }

            $body =
                "        \$argumentCount = \\func_num_args();\n" .
                '        $arguments = array();' .
                $argumentPacking .
                "\n\n        for (\$i = " .
                $parameterCount .
                "; \$i < \$argumentCount; ++\$i) {\n";

            if ($variadicIndex > -1) {
                $body .= "            \$arguments[] = $variadicReference\$a" .
                    "${variadicIndex}[\$i - $variadicIndex];\n";
            } else {
                $body .= "            \$arguments[] = \\func_get_arg(\$i);\n";
            }

            $body .=
                "        }\n\n        if (!${handle}) {\n";

            if ($isVoidReturn) {
                $resultAssign = '';
            } else {
                $resultAssign = '$result = ';
            }

            if ($isStatic) {
                $body .=  <<<EOD
            $resultAssign\call_user_func_array(
                array(__CLASS__, 'parent::' . $nameExported),
                \$arguments
            );
EOD;
            } else {
                $body .=  <<<EOD
            $resultAssign\call_user_func_array(
                array(\$this, 'parent::' . $nameExported),
                \$arguments
            );
EOD;
            }

            if ($isVoidReturn) {
                $body .=
                    "\n\n            return;\n        }\n\n" .
                    "        ${handle}->spy" .
                    "(__FUNCTION__)->invokeWith(\n" .
                    '            new \Eloquent\Phony\Call\Arguments' .
                    "(\$arguments)\n        );";
            } else {
                $body .=
                    "\n\n            return \$result;\n        }\n\n" .
                    "        \$result = ${handle}->spy" .
                    "(__FUNCTION__)->invokeWith(\n" .
                    '            new \Eloquent\Phony\Call\Arguments' .
                    "(\$arguments)\n        );\n\n" .
                    '        return $result;';
            }

            $returnsReference = $methodReflector->returnsReference() ? '&' : '';

            $source .= "\n    " .
                $method->accessLevel() .
                ' ' .
                $isStatic .
                'function ' .
                $returnsReference .
                $name;

            if (empty($signature)) {
                $source .= '()' . $returnType . "\n    {\n";
            } else {
                $index = -1;
                $isFirst = true;

                foreach ($signature as $parameter) {
                    if ($isFirst) {
                        $isFirst = false;
                        $source .= "(\n        ";
                    } else {
                        $source .= ",\n        ";
                    }

                    $source .= $parameter[0] .
                        $parameter[1] .
                        $parameter[2] .
                        '$a' .
                        ++$index .
                        $parameter[3];
                }

                $source .= "\n    )" . $returnType . " {\n";
            }

            $source .= $body . "\n    }\n";
        }

        return $source;
    }

    private function generateMagicCall($definition)
    {
        $methods = $definition->methods();
        $callName = $methods->methodName('__call');
        $methods = $methods->publicMethods();

        if (!$callName) {
            return '';
        }

        $methodReflector = $methods[$callName]->method();
        $returnsReference = $methodReflector->returnsReference() ? '&' : '';

        $source = <<<EOD

    public function ${returnsReference}__call(
EOD;
        $signature = $this->signatureInspector->signature($methodReflector);
        $index = -1;

        foreach ($signature as $parameter) {
            if (-1 !== $index) {
                $source .= ',';
            }

            $source .= "\n        " .
                $parameter[0] .
                $parameter[1] .
                '$a' .
                ++$index .
                $parameter[2];
        }

        if (
            $this->isReturnTypeSupported &&
            $methodReflector->hasReturnType()
        ) {
            $type = $methodReflector->getReturnType();
            $isBuiltin = $type->isBuiltin();

            if ($this->isHhvm) {
                // @codeCoverageIgnoreStart
                $typeString = $methodReflector->getReturnTypeText();

                if (0 === strpos($typeString, '?')) {
                    $typeString = '';
                } else {
                    $genericPosition = strpos($typeString, '<');

                    if (false !== $genericPosition) {
                        $typeString = substr($typeString, 0, $genericPosition);
                    }
                }

                $isBuiltin = $isBuiltin && false === strpos($typeString, '\\');
                // @codeCoverageIgnoreEnd
            } else {
                if ($type->allowsNull()) {
                    $typeString = '?' . $type;
                } else {
                    $typeString = (string) $type;
                }
            }

            if ('self' === $typeString) {
                $typeString = $methodReflector->getDeclaringClass()->getName();
            }

            if ($isBuiltin) {
                $source .= "\n    ) : " . $typeString . " {\n";
            } elseif (
                $this->isNullableTypeSupported &&
                0 === strpos($typeString, '?')
            ) {
                $source .= "\n    ) : ?\\" . substr($typeString, 1) . " {\n";
            } else {
                $source .= "\n    ) : \\" . $typeString . " {\n";
            }

            $isVoidReturn = $isBuiltin && 'void' === $typeString;
        } else {
            $source .= "\n    ) {\n";
            $isVoidReturn = false;
        }

        if ($isVoidReturn) {
            $source .= <<<'EOD'
        $this->_handle->spy($a0)
            ->invokeWith(new \Eloquent\Phony\Call\Arguments($a1));
    }

EOD;
        } else {
            $source .= <<<'EOD'
        $result = $this->_handle->spy($a0)
            ->invokeWith(new \Eloquent\Phony\Call\Arguments($a1));

        return $result;
    }

EOD;
        }

        return $source;
    }

    private function generateCallParentMethods($definition)
    {
        $methods = $definition->methods();
        $traitNames = $definition->traitNames();
        $hasTraits = (bool) $traitNames;
        $parentClassName = $definition->parentClassName();
        $hasParentClass = null !== $parentClassName;
        $constructor = null;
        $types = $definition->types();
        $source = '';

        if ($hasParentClass) {
            $source .= <<<'EOD'

    private static function _callParentStatic(
        $name,
        \Eloquent\Phony\Call\Arguments $arguments
    ) {
        return \call_user_func_array(
            array(__CLASS__, 'parent::' . $name),
            $arguments->all()
        );
    }

EOD;
        }

        if ($hasTraits) {
            $source .= <<<'EOD'

    private static function _callTraitStatic(
        $traitName,
        $name,
        \Eloquent\Phony\Call\Arguments $arguments
    ) {
        return \call_user_func_array(
            array(
                __CLASS__,
                '_callTrait_' .
                    \str_replace('\\', "\xc2\xa6", $traitName) .
                    "\xc2\xbb" .
                    $name,
            ),
            $arguments->all()
        );
    }

EOD;
        }

        if (null !== ($name = $methods->methodName('__callstatic'))) {
            $methodName = "'parent::__callStatic'";

            if ($hasTraits) {
                $methodsByName = $methods->staticMethods();

                if (
                    isset($methodsByName[$name]) &&
                    $methodsByName[$name] instanceof TraitMethodDefinition
                ) {
                    $traitName = $methodsByName[$name]
                        ->method()->getDeclaringClass()->getName();
                    $methodName = var_export(
                        'self::_callTrait_' .
                            \str_replace('\\', "\xc2\xa6", $traitName) .
                            "\xc2\xbb" .
                            $name,
                        true
                    );
                }
            }

            $source .= <<<EOD

    private static function _callMagicStatic(
        \$name,
        \Eloquent\Phony\Call\Arguments \$arguments
    ) {
        return \call_user_func_array(
            $methodName,
            array(\$name, \$arguments->all())
        );
    }

EOD;
        }

        if ($hasParentClass) {
            $source .= <<<'EOD'

    private function _callParent(
        $name,
        \Eloquent\Phony\Call\Arguments $arguments
    ) {
        return \call_user_func_array(
            array($this, 'parent::' . $name),
            $arguments->all()
        );
    }

EOD;

            $parentClass = $types[strtolower($parentClassName)];

            if ($constructor = $parentClass->getConstructor()) {
                $constructorName = $constructor->getName();

                if ($constructor->isPrivate()) {
                    if ($this->isClosureBindingSupported) {
                        $source .= <<<EOD

    private function _callParentConstructor(
        \Eloquent\Phony\Call\Arguments \$arguments
    ) {
        \$constructor = function () use (\$arguments) {
            \call_user_func_array(
                array(\$this, 'parent::$constructorName'),
                \$arguments->all()
            );
        };
        \$constructor = \$constructor->bindTo(\$this, '$parentClassName');
        \$constructor();
    }

EOD;
                    }
                } else {
                    $source .= <<<EOD

    private function _callParentConstructor(
        \Eloquent\Phony\Call\Arguments \$arguments
    ) {
        \call_user_func_array(
            array(\$this, 'parent::$constructorName'),
            \$arguments->all()
        );
    }

EOD;
                }
            }
        }

        if ($hasTraits) {
            if (!$constructor) {
                $constructorTraitName = null;

                foreach ($traitNames as $traitName) {
                    $trait = $types[strtolower($traitName)];

                    if ($traitConstructor = $trait->getConstructor()) {
                        $constructor = $traitConstructor;
                        $constructorTraitName = $trait->getName();
                    }
                }

                if ($constructor) {
                    $constructorName = '_callTrait_' .
                        \str_replace('\\', "\xc2\xa6", $constructorTraitName) .
                        "\xc2\xbb" .
                        $constructor->getName();

                    $source .= <<<EOD

    private function _callParentConstructor(
        \Eloquent\Phony\Call\Arguments \$arguments
    ) {
        \call_user_func_array(
            array(
                \$this,
                '$constructorName',
            ),
            \$arguments->all()
        );
    }

EOD;
                }
            }

            $source .= <<<'EOD'

    private function _callTrait(
        $traitName,
        $name,
        \Eloquent\Phony\Call\Arguments $arguments
    ) {
        return \call_user_func_array(
            array(
                $this,
                '_callTrait_' .
                    \str_replace('\\', "\xc2\xa6", $traitName) .
                    "\xc2\xbb" .
                    $name,
            ),
            $arguments->all()
        );
    }

EOD;
        }

        if (null !== ($name = $methods->methodName('__call'))) {
            $methodName = "'parent::__call'";

            if ($hasTraits) {
                $methodsByName = $methods->methods();

                if (
                    isset($methodsByName[$name]) &&
                    $methodsByName[$name] instanceof TraitMethodDefinition
                ) {
                    $traitName = $methodsByName[$name]
                        ->method()->getDeclaringClass()->getName();
                    $methodName = var_export(
                        '_callTrait_' .
                            \str_replace('\\', "\xc2\xa6", $traitName) .
                            "\xc2\xbb" .
                            $name,
                        true
                    );
                }
            }

            $source .= <<<EOD

    private function _callMagic(
        \$name,
        \Eloquent\Phony\Call\Arguments \$arguments
    ) {
        return \call_user_func_array(
            array(\$this, $methodName),
            array(\$name, \$arguments->all())
        );
    }

EOD;
        }

        return $source;
    }

    private function generateProperties($definition)
    {
        $staticProperties = $definition->customStaticProperties();
        $properties = $definition->customProperties();
        $source = '';

        foreach ($staticProperties as $name => $value) {
            $source .=
                "\n    public static $" .
                $name .
                ' = ' .
                (null === $value ? 'null' : var_export($value, true)) .
                ';';
        }

        foreach ($properties as $name => $value) {
            $source .=
                "\n    public $" .
                $name .
                ' = ' .
                (null === $value ? 'null' : var_export($value, true)) .
                ';';
        }

        $methods = $definition->methods()->allMethods();
        $uncallableMethodNames = array();
        $traitMethodNames = array();

        foreach ($methods as $methodName => $method) {
            $methodName = strtolower($methodName);

            if (!$method->isCallable()) {
                $uncallableMethodNames[$methodName] = true;
            } elseif ($method instanceof TraitMethodDefinition) {
                $traitMethodNames[$methodName] =
                    $method->method()->getDeclaringClass()->getName();
            }
        }

        $source .= "\n    private static \$_uncallableMethods = ";

        if (empty($uncallableMethodNames)) {
            $source .= 'array()';
        } else {
            $source .= var_export($uncallableMethodNames, true);
        }

        $source .= ";\n    private static \$_traitMethods = ";

        if (empty($traitMethodNames)) {
            $source .= 'array()';
        } else {
            $source .= var_export($traitMethodNames, true);
        }

        $source .= ";\n" .
            "    private static \$_customMethods = array();\n" .
            "    private static \$_staticHandle;\n" .
            '    private $_handle;';

        return $source;
    }

    private static $instance;
    private $labelSequencer;
    private $signatureInspector;
    private $featureDetector;
    private $isClosureBindingSupported;
    private $isReturnTypeSupported;
    private $isNullableTypeSupported;
    private $canMockPharDestruct;
    private $isHhvm;
}

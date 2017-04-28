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

use Eloquent\Phony\Reflection\Exception\UndefinedFeatureException;
use Exception;
use ReflectionClass;
use ReflectionException;
use ReflectionFunction;
use ReflectionMethod;
use Throwable;

/**
 * Detects support for language features in the current runtime environment.
 */
class FeatureDetector
{
    /**
     * Get the static instance of this detector.
     *
     * @return FeatureDetector The static detector.
     */
    public static function instance()
    {
        if (!self::$instance) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * Construct a new feature detector.
     *
     * @param array<string,callable>|null $features  The features.
     * @param array<string,bool>          $supported The known feature support.
     */
    public function __construct(
        array $features = null,
        array $supported = array()
    ) {
        if (null === $features) {
            $features = $this->standardFeatures();
        }

        $this->features = $features;
        $this->supported = $supported;

        $this->isErrorClearLastSupported = function_exists('error_clear_last');

        // @codeCoverageIgnoreStart
        $this->nullErrorHandler = function () {
            return false;
        };
        // @codeCoverageIgnoreEnd
    }

    /**
     * Add a custom feature.
     *
     * The callback will be passed this detector as the first argument. The
     * return value will be interpreted as a boolean.
     *
     * @param string   $feature  The feature.
     * @param callable $callback The feature detection callback.
     */
    public function addFeature($feature, $callback)
    {
        $this->features[$feature] = $callback;
    }

    /**
     * Get the features.
     *
     * @return array<string,callable> The features.
     */
    public function features()
    {
        return $this->features;
    }

    /**
     * Get the known feature support.
     *
     * @return array<string,bool> The known feature support.
     */
    public function supported()
    {
        return $this->supported;
    }

    /**
     * Returns true if the specified feature is supported by the current
     * runtime environment.
     *
     * @param string $feature The feature.
     *
     * @return bool                      True if supported.
     * @throws UndefinedFeatureException If the specified feature is undefined.
     */
    public function isSupported($feature)
    {
        if (!array_key_exists($feature, $this->supported)) {
            if (!isset($this->features[$feature])) {
                throw new UndefinedFeatureException($feature);
            }

            $this->supported[$feature] =
                (bool) call_user_func($this->features[$feature], $this);
        }

        return $this->supported[$feature];
    }

    /**
     * Get the standard feature detection callbacks.
     *
     * @return array<string,callable> The standard features.
     */
    public function standardFeatures()
    {
        return array(
            'class.anonymous' => function ($detector) {
                return $detector
                    ->checkInternalMethod('ReflectionClass', 'isAnonymous');
            },

            'closure' => function ($detector) {
                return $detector->checkInternalClass('Closure');
            },

            'closure.bind' => function ($detector) {
                return $detector->checkInternalMethod('Closure', 'bind');
            },

            'constant.array' => function ($detector) {
                // syntax causes fatal on PHP < 5.6
                if ($detector->isSupported('runtime.php')) {
                    if (version_compare(PHP_VERSION, '5.6.x', '<')) {
                        return false; // @codeCoverageIgnore
                    }
                }

                return $detector->checkStatement(
                    sprintf('const %s=array()', $detector->uniqueSymbolName()),
                    false
                );
            },

            'constant.class.array' => function ($detector) {
                // syntax causes fatal on PHP < 5.6
                if ($detector->isSupported('runtime.php')) {
                    if (version_compare(PHP_VERSION, '5.6.x', '<')) {
                        return false; // @codeCoverageIgnore
                    }
                }

                return $detector->checkStatement(
                    sprintf(
                        'class %s{const A=array();}',
                        $detector->uniqueSymbolName()
                    ),
                    false
                );
            },

            'constant.class.expression' => function ($detector) {
                return $detector->checkStatement(
                    sprintf(
                        'class %s{const A=0+0;}',
                        $detector->uniqueSymbolName()
                    ),
                    false
                );
            },

            'constant.expression' => function ($detector) {
                return $detector->checkStatement(
                    sprintf('const %s=0+0', $detector->uniqueSymbolName()),
                    false
                );
            },

            'error.exception.engine' => function ($detector) {
                return $detector->checkInternalClass('Error');
            },

            'generator' => function ($detector) {
                return $detector->checkInternalClass('Generator');
            },

            'generator.implicit-next' => function ($detector) {
                return $detector->isSupported('generator') &&
                    $detector->checkStatement(
                        '$f=function(){yield 0;yield 1;};$g=$f();$g->next();' .
                            'return 1===$g->current();',
                        false
                    );
            },

            'generator.exception' => function ($detector) {
                return $detector->checkInternalMethod('Generator', 'throw');
            },

            'generator.yield' => function ($detector) {
                return $detector->isSupported('generator') &&
                    $detector->checkStatement('yield 0', true);
            },

            'generator.yield.assign' => function ($detector) {
                return $detector->isSupported('generator') &&
                    $detector->checkStatement('$x=yield 0', true);
            },

            'generator.yield.assign.key' => function ($detector) {
                return $detector->isSupported('generator') &&
                    $detector->checkStatement('$x=yield 0=>0', true);
            },

            'generator.yield.assign.nothing' => function ($detector) {
                return $detector->isSupported('generator') &&
                    $detector->checkStatement('$x=yield', true);
            },

            'generator.yield.expression' => function ($detector) {
                return $detector->isSupported('generator') &&
                    $detector->checkStatement('(yield 0)', true);
            },

            'generator.yield.expression.assign' => function ($detector) {
                return $detector->isSupported('generator') &&
                    $detector->checkStatement('$x=(yield 0)', true);
            },

            'generator.yield.expression.assign.key' => function ($detector) {
                return $detector->isSupported('generator') &&
                    $detector->checkStatement('$x=(yield 0=>0)', true);
            },

            'generator.yield.expression.assign.nothing' => function ($detector) {
                return $detector->isSupported('generator') &&
                    $detector->checkStatement('$x=(yield)', true);
            },

            'generator.yield.expression.key' => function ($detector) {
                return $detector->isSupported('generator') &&
                    $detector->checkStatement('(yield 0=>0)', true);
            },

            'generator.yield.expression.nothing' => function ($detector) {
                return $detector->isSupported('generator') &&
                    $detector->checkStatement('(yield)', true);
            },

            'generator.yield.key' => function ($detector) {
                return $detector->isSupported('generator') &&
                    $detector->checkStatement('yield 0=>0', true);
            },

            'generator.yield.nothing' => function ($detector) {
                return $detector->isSupported('generator') &&
                    $detector->checkStatement('yield', true);
            },

            'generator.return' => function ($detector) {
                return $detector->checkInternalMethod('Generator', 'getReturn');
            },

            'object.constructor.php4' => function ($detector) {
                if ($detector->isSupported('runtime.hhvm')) {
                    return true; // @codeCoverageIgnore
                }

                return version_compare(PHP_VERSION, '7.x', '<');
            },

            'object.constructor.bypass' => function ($detector) {
                return $detector->checkInternalMethod(
                    'ReflectionClass',
                    'newInstanceWithoutConstructor'
                );
            },

            'object.constructor.bypass.extended-internal' => function (
                $detector
            ) {
                if (!$detector->isSupported('object.constructor.bypass')) {
                    return false; // @codeCoverageIgnore
                }

                $symbol = $detector->uniqueSymbolName();
                $exportedSymbol = var_export($symbol, true);

                return $detector->checkStatement(
                    sprintf(
                        'class %s extends ArrayObject{}' .
                            '$r=new ReflectionClass(%s);$o=0;try{' .
                            '$o=$r->newInstanceWithoutConstructor();' .
                            '}catch(Throwable $e){}catch(Exception $e){}' .
                            'return $o instanceof %s;',
                        $symbol,
                        $exportedSymbol,
                        $symbol
                    ),
                    false
                );
            },

            'parameter.default.constant' => function ($detector) {
                return $detector->checkInternalMethod(
                    'ReflectionParameter',
                    'isDefaultValueConstant'
                );
            },

            'parameter.type.self.override' => function ($detector) {
                if ($detector->isSupported('runtime.hhvm')) {
                    return true; // @codeCoverageIgnore
                }

                return !version_compare(PHP_VERSION, '5.4.1.x', '<');
            },

            'parameter.variadic' => function ($detector) {
                return $detector->checkStatement('function (...$a) {};', true);
            },

            'parameter.variadic.reference' => function ($detector) {
                return $detector->checkStatement('function (&...$a) {};', true);
            },

            'parameter.variadic.type' => function ($detector) {
                return $detector
                    ->checkStatement('function (stdClass ...$a) {};', true);
            },

            'parameter.hint.scalar' => function ($detector) {
                return $detector
                    ->checkInternalMethod('ReflectionParameter', 'getType');
            },

            'parser.relaxed-keywords' => function ($detector) {
                // syntax causes fatal on PHP < 7.0 and HHVM
                return $detector->isSupported('runtime.php') &&
                    !version_compare(PHP_VERSION, '7.x', '<');
            },

            'return.type' => function ($detector) {
                return $detector->checkInternalMethod(
                    'ReflectionFunctionAbstract',
                    'hasReturnType'
                );
            },

            'reflection.function.export.default.array' => function () {
                $function =
                    new ReflectionFunction(function ($a0 = array('a')) {});

                return false !== strpos(strval($function), "'a'");
            },

            'reflection.function.export.reference' => function () {
                $function = new ReflectionFunction(function (&$a0) {});

                return false !== strpos(strval($function), '&');
            },

            'runtime.hhvm' => function ($detector) {
                return 'hhvm' === $detector->runtime();
            },

            'runtime.php' => function ($detector) {
                return 'php' === $detector->runtime();
            },

            'stdout.ansi' => function () {
                // @codeCoverageIgnoreStart
                if (DIRECTORY_SEPARATOR === '\\') {
                    return
                        0 >= version_compare(
                        '10.0.10586',
                        PHP_WINDOWS_VERSION_MAJOR .
                            '.' . PHP_WINDOWS_VERSION_MINOR .
                            '.' . PHP_WINDOWS_VERSION_BUILD
                        ) ||
                        false !== getenv('ANSICON') ||
                        'ON' === getenv('ConEmuANSI') ||
                        'xterm' === getenv('TERM') ||
                        false !== getenv('BABUN_HOME');
                }
                // @codeCoverageIgnoreEnd

                return function_exists('posix_isatty') && @posix_isatty(STDOUT);
            },

            'trait' => function ($detector) {
                return $detector
                    ->checkInternalMethod('ReflectionClass', 'isTrait');
            },

            'type.callable' => function ($detector) {
                return $detector
                    ->checkInternalMethod('ReflectionParameter', 'isCallable');
            },

            'type.iterable' => function () {
                try {
                    $function =
                        new ReflectionFunction(function (iterable $a) {});
                    $parameters = $function->getParameters();
                    $result = null === $parameters[0]->getClass();
                    // @codeCoverageIgnoreStart
                } catch (ReflectionException $e) {
                    $result = false;
                }
                // @codeCoverageIgnoreEnd

                return $result;
            },

            'type.nullable' => function ($detector) {
                // syntax causes fatal on HHVM
                if ($detector->isSupported('runtime.hhvm')) {
                    return false; // @codeCoverageIgnore
                }

                return $detector->checkStatement('function(?int $a){}', false);
            },

            'type.void' => function ($detector) {
                // @codeCoverageIgnoreStart
                if (!$detector->isSupported('return.type')) {
                    return false;
                }
                // @codeCoverageIgnoreEnd

                return $detector->checkStatement(
                    '$r=new ReflectionFunction(function():void{});' .
                        'return $r->getReturnType()->isBuiltin();',
                    false
                );
            },
        );
    }

    /**
     * Determine the current PHP runtime.
     *
     * @return string The runtime.
     */
    public function runtime()
    {
        if (!$this->runtime) {
            if (false === strpos(phpversion(), 'hhvm')) {
                $this->runtime = 'php';
            } else {
                $this->runtime = 'hhvm'; // @codeCoverageIgnore
            }
        }

        return $this->runtime;
    }

    /**
     * Check that a keyword is interpreted as a particular token type.
     *
     * @param string $keyword      The keyword.
     * @param string $constantName The name of the token type constant.
     *
     * @return bool True if the keyword is interpreted as expected.
     */
    public function checkToken($keyword, $constantName)
    {
        if (!defined($constantName)) {
            return false;
        }

        $tokens = token_get_all('<?php ' . $keyword);

        return is_array($tokens[1]) &&
            constant($constantName) === $tokens[1][0];
    }

    /**
     * Check that the supplied syntax is valid.
     *
     * @param string $source     The source to check.
     * @param bool   $useClosure True to wrap the supplied source code in a closure.
     *
     * @return bool True if the syntax is valid.
     */
    public function checkStatement($source, $useClosure = true)
    {
        $reporting = error_reporting(E_ERROR | E_COMPILE_ERROR);
        $result = false;

        if ($useClosure) {
            try {
                $result = eval(sprintf('function(){%s;};return true;', $source));
            } catch (Throwable $e) {
                // @codeCoverageIgnoreStart
            } catch (Exception $e) {
            }
            // @codeCoverageIgnoreEnd
        } else {
            try {
                $result = eval(sprintf('%s;return true;', $source));
            } catch (Throwable $e) {
                // @codeCoverageIgnoreStart
            } catch (Exception $e) {
            }
            // @codeCoverageIgnoreEnd
        }

        if (false === $result) {
            if ($this->isErrorClearLastSupported) {
                error_clear_last();
                // @codeCoverageIgnoreStart
            } else {
                set_error_handler($this->nullErrorHandler);
                @trigger_error('');
                restore_error_handler();
            }
            // @codeCoverageIgnoreEnd
        }

        error_reporting($reporting);

        return true === $result;
    }

    /**
     * Check that the specified class is defined by the PHP core, or an
     * extension.
     *
     * @param string $className The class name.
     *
     * @return bool True if the class exists, and is internal.
     */
    public function checkInternalClass($className)
    {
        if (class_exists($className, false)) {
            $class = new ReflectionClass($className);

            return $class->isInternal();
        }

        return false;
    }

    /**
     * Check that the specified method is defined by the PHP core, or an
     * extension.
     *
     * @param string $className  The class name.
     * @param string $methodName The class name.
     *
     * @return bool True if the method exists, and is internal.
     */
    public function checkInternalMethod($className, $methodName)
    {
        if (!class_exists($className, false)) {
            return false;
        }

        if (method_exists($className, $methodName)) {
            $method = new ReflectionMethod($className, $methodName);

            return $method->isInternal();
        }

        return false;
    }

    /**
     * Returns a symbol name that is unique for this process execution.
     *
     * @return string The symbol name.
     */
    public function uniqueSymbolName()
    {
        return sprintf('_FD_symbol_%s', md5(uniqid()));
    }

    private static $instance;
    private $features;
    private $supported;
    private $runtime;
    private $isErrorClearLastSupported;
    private $nullErrorHandler;
}

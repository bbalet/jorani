<?php

/*
 * This file is part of the Phony package.
 *
 * Copyright © 2016 Erin Millard
 *
 * For the full copyright and license information, please view the LICENSE file
 * that was distributed with this source code.
 */

namespace Eloquent\Phony\Hook;

use Eloquent\Phony\Hook\Exception\FunctionExistsException;
use Eloquent\Phony\Hook\Exception\FunctionHookException;
use Eloquent\Phony\Hook\Exception\FunctionHookGenerationFailedException;
use Eloquent\Phony\Hook\Exception\FunctionSignatureMismatchException;
use Eloquent\Phony\Reflection\FunctionSignatureInspector;
use Exception;
use ParseError;
use ParseException;
use Throwable;

/**
 * Allows control over the behavior of function hooks.
 */
class FunctionHookManager
{
    /**
     * Get the static instance of this manager.
     *
     * @return FunctionHookManager The static manager.
     */
    public static function instance()
    {
        if (!self::$instance) {
            self::$instance = new self(
                FunctionSignatureInspector::instance(),
                FunctionHookGenerator::instance()
            );
        }

        return self::$instance;
    }

    /**
     * Construct a new function hook manager.
     *
     * @param FunctionSignatureInspector $signatureInspector The function signature inspector to use.
     * @param FunctionHookGenerator      $hookGenerator      The function hook generator to use.
     */
    public function __construct(
        FunctionSignatureInspector $signatureInspector,
        FunctionHookGenerator $hookGenerator
    ) {
        $this->signatureInspector = $signatureInspector;
        $this->hookGenerator = $hookGenerator;
    }

    /**
     * Define the behavior of a function hook.
     *
     * @param string   $name      The function name.
     * @param string   $namespace The namespace.
     * @param callable $callback  The callback.
     *
     * @return callback|null         The replaced callback, or null if no callback was set.
     * @throws FunctionHookException If the function hook generation fails.
     */
    public function defineFunction($name, $namespace, $callback)
    {
        $signature = $this->signatureInspector->callbackSignature($callback);
        $fullName = $namespace . '\\' . $name;
        $key = strtolower($fullName);

        if (isset(self::$hooks[$key])) {
            if ($signature !== self::$hooks[$key]['signature']) {
                throw new FunctionSignatureMismatchException($fullName);
            }

            $replaced = self::$hooks[$key]['callback'];
        } else {
            $replaced = null;

            if (function_exists($fullName)) {
                throw new FunctionExistsException($fullName);
            }

            $source = $this->hookGenerator
                ->generateHook($name, $namespace, $signature);
            $reporting = error_reporting(E_ERROR | E_COMPILE_ERROR);
            $error = null;

            try {
                eval($source);
            } catch (ParseError $e) {
                $error = new FunctionHookGenerationFailedException(
                    $fullName,
                    $callback,
                    $source,
                    error_get_last(),
                    $e
                );
                // @codeCoverageIgnoreStart
            } catch (ParseException $e) {
                $error = new FunctionHookGenerationFailedException(
                    $fullName,
                    $callback,
                    $source,
                    error_get_last(),
                    $e
                );
            } catch (Throwable $error) {
                // re-thrown after cleanup
            } catch (Exception $error) {
                // re-thrown after cleanup
            }
            // @codeCoverageIgnoreEnd

            error_reporting($reporting);

            if ($error) {
                throw $error;
            }

            if (!function_exists($fullName)) {
                // @codeCoverageIgnoreStart
                throw new FunctionHookGenerationFailedException(
                    $fullName,
                    $callback,
                    $source,
                    error_get_last()
                );
                // @codeCoverageIgnoreEnd
            }
        }

        self::$hooks[$key] =
            array('callback' => $callback, 'signature' => $signature);

        return $replaced;
    }

    /**
     * Effectively removes any function hooks for functions in the global
     * namespace.
     */
    public function restoreGlobalFunctions()
    {
        foreach (self::$hooks as $key => $data) {
            self::$hooks[$key]['callback'] = null;
        }
    }

    public static $hooks = array();
    private static $instance;
    private $signatureInspector;
    private $hookGenerator;
}

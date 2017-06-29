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

/**
 * Generates the source code for function hooks.
 */
class FunctionHookGenerator
{
    /**
     * Get the static instance of this generator.
     *
     * @return FunctionHookGenerator The static generator.
     */
    public static function instance()
    {
        if (!self::$instance) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * Generate the source code for a function hook.
     *
     * @param string                      $name      The function name.
     * @param string                      $namespace The namespace.
     * @param array<string,array<string>> $signature The function signature.
     *
     * @return string The source code.
     */
    public function generateHook($name, $namespace, array $signature)
    {
        $source = "namespace $namespace;\n\nfunction $name";
        $parameterCount = count($signature);

        if ($parameterCount > 0) {
            $index = -1;
            $isFirst = true;

            foreach ($signature as $parameter) {
                if ($isFirst) {
                    $isFirst = false;
                    $source .= "(\n    ";
                } else {
                    $source .= ",\n    ";
                }

                $source .= $parameter[0] .
                    $parameter[1] .
                    $parameter[2] .
                    '$a' .
                    ++$index .
                    $parameter[3];
            }

            $source .= "\n) {\n";
        } else {
            $source .= "()\n{\n";
        }

        $variadicIndex = -1;
        $variadicReference = '';

        if ($parameterCount > 0) {
            $argumentPacking = "\n";
            $index = -1;

            foreach ($signature as $parameter) {
                if ($parameter[2]) {
                    --$parameterCount;

                    $variadicIndex = ++$index;
                    $variadicReference = $parameter[1];
                } else {
                    $argumentPacking .=
                        "\n    if (\$argumentCount > " .
                        ++$index .
                        ") {\n        \$arguments[] = " .
                        $parameter[1] .
                        '$a' .
                        $index .
                        ";\n    }";
                }
            }
        } else {
            $argumentPacking = '';
        }

        $source .=
            "    \$argumentCount = \\func_num_args();\n" .
            '    $arguments = array();' .
            $argumentPacking .
            "\n\n    for (\$i = " .
            $parameterCount .
            "; \$i < \$argumentCount; ++\$i) {\n";

        if ($variadicIndex > -1) {
            $source .=
                "        \$arguments[] = $variadicReference\$a" .
                $variadicIndex . "[\$i - $variadicIndex];\n" .
                '    }';
        } else {
            $source .=
                "        \$arguments[] = \\func_get_arg(\$i);\n" .
                '    }';
        }

        $ret = 'ret' . 'urn';

        $renderedName = var_export(strtolower($namespace . '\\' . $name), true);
        $renderedShortName = var_export($name, true);
        $source .=
            "\n\n    \$name = $renderedName;\n\n    if (" .
            "\n        !isset(\n            " .
            '\Eloquent\Phony\Hook\FunctionHookManager::$hooks[$name]' .
            "['callback']\n        )\n    ) {\n        " .
            "$ret \call_user_func_array($renderedShortName, \$arguments);" .
            "\n    }\n\n    \$callback =\n        " .
            '\Eloquent\Phony\Hook\FunctionHookManager::$hooks' .
            "[\$name]['callback'];\n\n" .
            '    if ($callback instanceof ' .
            "\Eloquent\Phony\Invocation\Invocable) {\n" .
            "        $ret \$callback->invokeWith(\$arguments);\n" .
            "    }\n\n    " .
            "$ret \\call_user_func_array(\$callback, \$arguments);\n}\n";

        // @codeCoverageIgnoreStart
        if ("\n" !== PHP_EOL) {
            $source = str_replace("\n", PHP_EOL, $source);
        }
        // @codeCoverageIgnoreEnd

        return $source;
    }

    private static $instance;
}

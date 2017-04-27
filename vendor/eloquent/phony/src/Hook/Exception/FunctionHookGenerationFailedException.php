<?php

/*
 * This file is part of the Phony package.
 *
 * Copyright © 2016 Erin Millard
 *
 * For the full copyright and license information, please view the LICENSE file
 * that was distributed with this source code.
 */

namespace Eloquent\Phony\Hook\Exception;

use Error;
use Exception;

/**
 * Mock generation failed.
 */
final class FunctionHookGenerationFailedException extends Exception implements
    FunctionHookException
{
    /**
     * Construct a mock generation failed exception.
     *
     * @param string                   $functionName The function name.
     * @param callable                 $callback     The callback.
     * @param string                   $source       The generated source code.
     * @param array<string,mixed>|null $error        The error details.
     * @param Exception|Error|null     $cause        The cause, if available.
     */
    public function __construct(
        $functionName,
        $callback,
        $source,
        array $error = null,
        $cause = null
    ) {
        $this->functionName = $functionName;
        $this->callback = $callback;
        $this->source = $source;
        $this->error = $error;

        $lines = explode(PHP_EOL, $source);

        if (null === $error) {
            $message = sprintf(
                'Function hook %s generation failed.%sRelevant lines:%%s',
                $functionName,
                PHP_EOL
            );
            $errorLineNumber = null;
        } else {
            $errorLineNumber = $error['line'];
            $startLine = $errorLineNumber - 4;
            $contextLineCount = 7;

            if ($startLine < 0) {
                $contextLineCount += $startLine;
                $startLine = 0;
            }

            $lines = array_slice($lines, $startLine, $contextLineCount, true);

            $message = sprintf(
                'Function hook %s generation failed: ' .
                    '%s in generated code on line %d.%s' .
                    'Relevant lines:%%s',
                $functionName,
                $error['message'],
                $errorLineNumber,
                PHP_EOL
            );
        }

        end($lines);
        $lineNumber = key($lines);
        $padSize = strlen($lineNumber + 1) + 4;
        $renderedLines = '';

        foreach ($lines as $lineNumber => $line) {
            if (null !== $errorLineNumber) {
                $highlight = $lineNumber + 1 === $errorLineNumber;
            } else {
                $highlight = false;
            }

            $renderedLines .= sprintf(
                '%s%s%s %s',
                PHP_EOL,
                str_pad($lineNumber + 1, $padSize, ' ', STR_PAD_LEFT),
                $highlight ? ':' : ' ',
                $line
            );
        }

        parent::__construct(sprintf($message, $renderedLines), 0, $cause);
    }

    /**
     * Get the function name.
     *
     * @return string The function name.
     */
    public function functionName()
    {
        return $this->functionName;
    }

    /**
     * Get the callback.
     *
     * @return callable The callback.
     */
    public function callback()
    {
        return $this->callback;
    }

    /**
     * Get the generated source code.
     *
     * @return string The generated source code.
     */
    public function source()
    {
        return $this->source;
    }

    /**
     * Get the error details.
     *
     * @return array<string,mixed> The error details.
     */
    public function error()
    {
        return $this->error;
    }

    private $functionName;
    private $callback;
    private $source;
    private $error;
}

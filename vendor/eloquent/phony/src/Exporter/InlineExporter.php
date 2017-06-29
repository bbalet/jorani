<?php

/*
 * This file is part of the Phony package.
 *
 * Copyright © 2016 Erin Millard
 *
 * For the full copyright and license information, please view the LICENSE file
 * that was distributed with this source code.
 */

namespace Eloquent\Phony\Exporter;

use Closure;
use Eloquent\Phony\Invocation\InvocableInspector;
use Eloquent\Phony\Invocation\WrappedInvocable;
use Eloquent\Phony\Mock\Handle\Handle;
use Eloquent\Phony\Mock\Handle\InstanceHandle;
use Eloquent\Phony\Mock\Handle\StaticHandle;
use Eloquent\Phony\Mock\Method\WrappedMethod;
use Eloquent\Phony\Mock\Mock;
use Eloquent\Phony\Sequencer\Sequencer;
use Eloquent\Phony\Spy\IterableSpy;
use Eloquent\Phony\Spy\Spy;
use Eloquent\Phony\Spy\SpyVerifier;
use Eloquent\Phony\Stub\Stub;
use Eloquent\Phony\Stub\StubVerifier;
use Exception;
use Generator;
use ReflectionException;
use ReflectionFunction;
use ReflectionMethod;
use SplObjectStorage;
use Throwable;

/**
 * Exports values to inline strings.
 */
class InlineExporter implements Exporter
{
    /**
     * Get the static instance of this exporter.
     *
     * @return Exporter The static exporter.
     */
    public static function instance()
    {
        if (!self::$instance) {
            self::$instance = new self(
                1,
                Sequencer::sequence('exporter-object-id'),
                InvocableInspector::instance()
            );
        }

        return self::$instance;
    }

    /**
     * Construct a new inline exporter.
     *
     * @param int                $depth              The depth.
     * @param Sequencer          $objectSequencer    The object sequencer to use.
     * @param InvocableInspector $invocableInspector The invocable inspector to use.
     */
    public function __construct(
        $depth,
        Sequencer $objectSequencer,
        InvocableInspector $invocableInspector
    ) {
        $this->depth = $depth;
        $this->objectSequencer = $objectSequencer;
        $this->invocableInspector = $invocableInspector;
        $this->objectIds = array();
        $this->jsonFlags = 0;

        if (defined('JSON_UNESCAPED_SLASHES')) {
            $this->jsonFlags |= JSON_UNESCAPED_SLASHES;
        }
        if (defined('JSON_UNESCAPED_UNICODE')) {
            $this->jsonFlags |= JSON_UNESCAPED_UNICODE;
        }
    }

    /**
     * Set the default depth.
     *
     * Negative depths are treated as infinite depth.
     *
     * @param int $depth The depth.
     *
     * @return int The previous depth.
     */
    public function setDepth($depth)
    {
        $oldDepth = $this->depth;
        $this->depth = $depth;

        return $oldDepth;
    }

    /**
     * Export the supplied value.
     *
     * Negative depths are treated as infinite depth.
     *
     * @param mixed    &$value The value.
     * @param int|null $depth  The depth, or null to use the default.
     *
     * @return string The exported value.
     */
    public function export(&$value, $depth = null)
    {
        if (null === $depth) {
            $depth = $this->depth;
        }

        $final = (object) array();
        $stack = array(array(&$value, $final, 0, gettype($value)));
        $results = array();
        $seenWrappers = new SplObjectStorage();
        $seenObjects = new SplObjectStorage();
        $seenArrays = array();
        $arrayResults = array();
        $arrayId = 0;

        while (!empty($stack)) {
            $entry = array_shift($stack);
            $value = &$entry[0];
            $result = $entry[1];
            $currentDepth = $entry[2];
            $type = $entry[3];
            $results[] = $result;

            switch ($type) {
                case 'NULL':
                    $result->type = 'null';

                    break;

                case 'boolean':
                    if ($value) {
                        $result->type = 'true';
                    } else {
                        $result->type = 'false';
                    }

                    break;

                case 'integer':
                    $result->type = strval($value);

                    break;

                case 'double':
                    $result->type = sprintf('%e', $value);

                    break;

                case 'resource':
                    $result->type = 'resource#' . intval($value);

                    break;

                case 'string':
                    $result->type = json_encode($value, $this->jsonFlags);

                    break;

                case 'array':
                    if (isset($value[self::ARRAY_ID_KEY])) {
                        $id = $value[self::ARRAY_ID_KEY];
                    } else {
                        $id = $value[self::ARRAY_ID_KEY] = $arrayId++;
                    }

                    $seenArrays[$id] = &$value;

                    if (isset($arrayResults[$id])) {
                        $result->type = '&' . $id . '[]';

                        break;
                    }

                    $result->type = '#' . $id;

                    if ($depth > -1 && $currentDepth >= $depth) {
                        $count = count($value) - 1;

                        if ($count) {
                            $result->type .= '[~' . $count . ']';
                        } else {
                            $result->type .= '[]';
                        }

                        break;
                    }

                    $arrayResults[$id] = $result;

                    $result->children = array();
                    $result->sequence = true;
                    $sequenceKey = 0;

                    foreach ($value as $key => &$childValue) {
                        if (self::ARRAY_ID_KEY === $key) {
                            continue;
                        }

                        if ($result->sequence) {
                            if ($key !== $sequenceKey++) {
                                $result->map = true;
                                $result->sequence = false;
                            }
                        }

                        $keyResult = (object) array();
                        $valueResult = (object) array();
                        $result->children[] = array($keyResult, $valueResult);

                        $stack[] = array(
                            $key,
                            $keyResult,
                            $currentDepth + 1,
                            gettype($key),
                        );
                        $stack[] = array(
                            &$childValue,
                            $valueResult,
                            $currentDepth + 1,
                            gettype($childValue),
                        );
                    }

                    break;

                case 'object':
                    $hash = spl_object_hash($value);

                    if (isset($this->objectIds[$hash])) {
                        $id = $this->objectIds[$hash];
                    } else {
                        $id = $this->objectIds[$hash] =
                            $this->objectSequencer->next();
                    }

                    if ($seenWrappers->contains($value)) {
                        $result->type = '&' . $id . '()';

                        break;
                    }

                    if ($seenObjects->contains($value)) {
                        $result->type = '&' . $id . '{}';

                        break;
                    }

                    if ($value instanceof Closure) {
                        $isWrapper = false;
                        $isClosure = true;
                        $isException = false;
                        $isHandle = false;
                        $isSpy = false;
                        $isStub = false;
                        $isGeneratorSpy = false;
                        $isIterableSpy = false;
                    } elseif (
                        $value instanceof Throwable ||
                        $value instanceof Exception
                    ) {
                        $isWrapper = false;
                        $isClosure = false;
                        $isException = true;
                        $isHandle = false;
                        $isSpy = false;
                        $isStub = false;
                        $isGeneratorSpy = false;
                        $isIterableSpy = false;
                    } elseif ($value instanceof Generator) {
                        $isWrapper = isset($value->_phonySubject);
                        $isClosure = false;
                        $isException = false;
                        $isHandle = false;
                        $isSpy = false;
                        $isStub = false;
                        $isGeneratorSpy = $isWrapper;
                        $isIterableSpy = false;
                    } elseif ($value instanceof Handle) {
                        $isWrapper = true;
                        $isClosure = false;
                        $isException = false;
                        $isHandle = true;
                        $isSpy = false;
                        $isStub = false;
                        $isGeneratorSpy = false;
                        $isIterableSpy = false;

                        $isStaticHandle = $value instanceof StaticHandle;
                    } elseif ($value instanceof Stub) {
                        $isWrapper = true;
                        $isClosure = false;
                        $isException = false;
                        $isHandle = false;
                        $isSpy = false;
                        $isStub = true;
                        $isGeneratorSpy = false;
                        $isIterableSpy = false;

                        $isStubVerifier = $value instanceof StubVerifier;
                    } elseif ($value instanceof Spy) {
                        $isWrapper = true;
                        $isClosure = false;
                        $isException = false;
                        $isHandle = false;
                        $isSpy = true;
                        $isStub = false;
                        $isGeneratorSpy = false;
                        $isIterableSpy = false;

                        $isSpyVerifier = $value instanceof SpyVerifier;
                    } elseif ($value instanceof IterableSpy) {
                        $isWrapper = true;
                        $isClosure = false;
                        $isException = false;
                        $isHandle = false;
                        $isSpy = false;
                        $isStub = false;
                        $isGeneratorSpy = false;
                        $isIterableSpy = true;
                    } else {
                        $isWrapper = false;
                        $isClosure = false;
                        $isException = false;
                        $isHandle = false;
                        $isSpy = false;
                        $isStub = false;
                        $isGeneratorSpy = false;
                        $isIterableSpy = false;
                    }

                    $isMock = $value instanceof Mock;

                    if ($isClosure) {
                        $result->type = 'Closure';
                    } elseif ($isHandle) {
                        if ($isStaticHandle) {
                            $result->type = 'static-handle';
                        } else {
                            $result->type = 'handle';
                        }
                    } elseif ($isStub) {
                        $result->type = 'stub';
                    } elseif ($isSpy) {
                        $result->type = 'spy';
                    } elseif ($isGeneratorSpy) {
                        $result->type = 'generator-spy';
                    } elseif ($isIterableSpy) {
                        $result->type = 'iterable-spy';
                    } else {
                        $result->type = get_class($value);
                    }

                    $phpValues = (array) $value;

                    if ($isHandle) {
                        if ($isStaticHandle) {
                            $result->child = (object) array(
                                'final' => $phpValues["\0*\0class"]->getName(),
                            );
                        } else {
                            $result->child = (object) array();
                            $stack[] = array(
                                $phpValues["\0*\0mock"],
                                $result->child,
                                $currentDepth,
                                'object',
                            );
                        }
                    } elseif ($isSpy) {
                        if ($isSpyVerifier) {
                            $phpValues = (array) $phpValues[
                                "\0Eloquent\Phony\Spy\SpyVerifier\0spy"
                            ];
                        }

                        if (!$phpValues["\0*\0isAnonymous"]) {
                            $result->child = (object) array(
                                'final' => $this->exportCallable(
                                    $phpValues["\0*\0callback"]
                                ),
                            );
                        }

                        $result->label = $phpValues["\0*\0label"];
                    } elseif ($isStub) {
                        if ($isStubVerifier) {
                            $phpValues = (array) $phpValues[
                                "\0Eloquent\Phony\Stub\StubVerifier\0stub"
                            ];
                        }

                        if (!$phpValues["\0*\0isAnonymous"]) {
                            $result->child = (object) array(
                                'final' => $this->exportCallable(
                                    $phpValues["\0*\0callback"]
                                ),
                            );
                        }

                        $result->label = $phpValues["\0*\0label"];
                    } elseif ($isGeneratorSpy) {
                        $result->child = (object) array();
                        $stack[] = array(
                            $value->_phonySubject,
                            $result->child,
                            $currentDepth,
                            'object',
                        );
                    } elseif ($isIterableSpy) {
                        $iterable = $value->iterable();
                        $result->child = (object) array();
                        $stack[] = array(
                            $iterable,
                            $result->child,
                            $currentDepth,
                            gettype($iterable),
                        );
                    }

                    if ($isWrapper) {
                        $result->wrapper = true;
                        $result->type .= '#' . $id;
                        $seenWrappers->offsetSet($value, true);
                    } else {
                        unset($phpValues["\0gcdata"]);

                        if ($isMock) {
                            $handleProperty =
                                "\0" . $result->type . "\0_handle";

                            if ($phpValues[$handleProperty]) {
                                $result->label =
                                    $phpValues[$handleProperty]->label();
                            }

                            unset($phpValues[$handleProperty]);
                        }

                        if ($isException) {
                            unset(
                                $phpValues["\0*\0file"],
                                $phpValues["\0*\0line"],
                                $phpValues["\0Exception\0trace"],
                                $phpValues["\0Exception\0string"],
                                $phpValues['xdebug_message']
                            );
                        } elseif ($isClosure) {
                            $reflector = new ReflectionFunction($value);
                            $result->label =
                                basename($reflector->getFilename()) . ':' .
                                $reflector->getStartLine();
                            $phpValues = array();
                        }

                        $properties = array();
                        $propertyCounts = array();

                        foreach (
                            $phpValues as $propertyName => $propertyValue
                        ) {
                            if (
                                preg_match(
                                    '/^\x00([^\x00]+)\x00([^\x00]+)$/',
                                    $propertyName,
                                    $matches
                                )
                            ) {
                                if (
                                    '*' === $matches[1] ||
                                    $result->type === $matches[1]
                                ) {
                                    $propertyName = $realName = $matches[2];
                                } else {
                                    $propertyName = $matches[2];
                                    $realName =
                                        $matches[1] . '.' . $propertyName;
                                }

                                $properties[] = array(
                                    $propertyName,
                                    $realName,
                                    $propertyValue,
                                );
                            } else {
                                $properties[] = array(
                                    $propertyName,
                                    $propertyName,
                                    $propertyValue,
                                );
                            }

                            if (isset($propertyCounts[$propertyName])) {
                                $propertyCounts[$propertyName] += 1;
                            } else {
                                $propertyCounts[$propertyName] = 1;
                            }
                        }

                        $values = array();

                        foreach ($properties as $property) {
                            list($shortName, $realName, $propertyValue) =
                                $property;

                            if ($propertyCounts[$shortName] > 1) {
                                $values[$realName] = $propertyValue;
                            } else {
                                $values[$shortName] = $propertyValue;
                            }
                        }

                        if ($isException) {
                            if ('' === $values['message']) {
                                unset($values['message']);
                            }
                            if (0 === $values['code']) {
                                unset($values['code']);
                            }
                            if (!$values['previous']) {
                                unset($values['previous']);
                            }
                        }

                        if ('stdClass' === $result->type) {
                            $result->type = '';
                        }

                        $result->type .= '#' . $id;

                        if ($depth > -1 && $currentDepth >= $depth) {
                            if (empty($values)) {
                                $result->type .= '{}';
                            } else {
                                $result->type .= '{~' . count($values) . '}';
                            }

                            break;
                        }

                        $seenObjects->offsetSet($value, true);

                        $result->children = array();
                        $result->object = true;

                        foreach ($values as $key => &$childValue) {
                            $valueResult = (object) array();
                            $result->children[] = array($key, $valueResult);

                            $stack[] = array(
                                &$childValue,
                                $valueResult,
                                $currentDepth + 1,
                                gettype($childValue),
                            );
                        }
                    }

                    break;

                // @codeCoverageIgnoreStart
                default:
                    $result->type = '???';
                // @codeCoverageIgnoreEnd
            }
        }

        foreach (array_reverse($results) as $result) {
            $result->final = $result->type;

            if (isset($result->wrapper)) {
                if (isset($result->child)) {
                    $result->final .= '(' . $result->child->final . ')';
                }
            } elseif (isset($result->object)) {
                $result->final .= '{';
                $isFirst = true;

                foreach ($result->children as $pair) {
                    if (!$isFirst) {
                        $result->final .= ', ';
                    }

                    $result->final .= $pair[0] . ': ' . $pair[1]->final;
                    $isFirst = false;
                }

                $result->final .= '}';
            } elseif (isset($result->map)) {
                $result->final .= '[';
                $isFirst = true;

                foreach ($result->children as $pair) {
                    if (!$isFirst) {
                        $result->final .= ', ';
                    }

                    $result->final .=
                        $pair[0]->final . ': ' . $pair[1]->final;
                    $isFirst = false;
                }

                $result->final .= ']';
            } elseif (isset($result->sequence)) {
                $result->final .= '[';
                $isFirst = true;

                foreach ($result->children as $pair) {
                    if (!$isFirst) {
                        $result->final .= ', ';
                    }

                    $result->final .= $pair[1]->final;
                    $isFirst = false;
                }

                $result->final .= ']';
            }

            if (isset($result->label)) {
                $result->final .= '[' . $result->label . ']';
            }
        }

        foreach ($seenArrays as &$value) {
            unset($value[self::ARRAY_ID_KEY]);
        }

        return $final->final;
    }

    /**
     * Export a string representation of a callable value.
     *
     * @param callable $callback The callable.
     *
     * @return string The exported callable.
     */
    public function exportCallable($callback)
    {
        $wrappedCallback = null;

        while ($callback instanceof WrappedInvocable) {
            $wrappedCallback = $callback;
            $callback = $callback->callback();
        }

        $label = '';

        if ($wrappedCallback) {
            if ($wrappedCallback->isAnonymous()) {
                return $this->export($wrappedCallback);
            }

            $label = $wrappedCallback->label();

            if (null !== $label) {
                $label = '[' . $label . ']';
            }
        }

        if ($callback instanceof Closure) {
            return $this->export($callback) . $label;
        }

        $reflector = $this->invocableInspector->callbackReflector($callback);

        if (!$reflector instanceof ReflectionMethod) {
            return $reflector->getName() . $label;
        }

        $class = $reflector->getDeclaringClass();
        $name = $reflector->getName();

        if ($class->implementsInterface('Eloquent\Phony\Mock\Mock')) {
            if (
                ($parentClass = $class->getParentClass()) &&
                $parentClass->hasMethod($name)
            ) {
                $class = $parentClass;
            } else {
                try {
                    $prototype = $reflector->getPrototype();
                    $class = $prototype->getDeclaringClass();
                } catch (ReflectionException $e) {
                    // ignore
                }
            }
        }

        $atoms = explode('\\', $class->getName());
        $rendered = array_pop($atoms);

        if ($wrappedCallback instanceof WrappedMethod) {
            $name = $wrappedCallback->name();
            $handle = $wrappedCallback->handle();

            if ($handle instanceof InstanceHandle) {
                $label = $handle->label();

                if (null !== $label) {
                    $rendered .= '[' . $label . ']';
                }
            }
        }

        if ($reflector->isStatic()) {
            $callOperator = '::';
        } else {
            $callOperator = '->';
        }

        return $rendered . $callOperator . $name;
    }

    /**
     * Reset the internal state of the exporter.
     *
     * Used for testing purposes only.
     */
    public function reset()
    {
        $this->objectIds = array();
        $this->objectSequencer->reset();
    }

    const ARRAY_ID_KEY = "\0__phony__\0";

    private static $instance;
    private $depth;
    private $objectSequencer;
    private $invocableInspector;
    private $objectIds;
    private $jsonFlags;
}

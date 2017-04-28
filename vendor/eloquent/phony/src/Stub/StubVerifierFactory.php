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

use Eloquent\Phony\Assertion\AssertionRecorder;
use Eloquent\Phony\Assertion\AssertionRenderer;
use Eloquent\Phony\Assertion\ExceptionAssertionRecorder;
use Eloquent\Phony\Call\CallVerifierFactory;
use Eloquent\Phony\Hook\FunctionHookManager;
use Eloquent\Phony\Matcher\MatcherFactory;
use Eloquent\Phony\Matcher\MatcherVerifier;
use Eloquent\Phony\Spy\Spy;
use Eloquent\Phony\Spy\SpyFactory;
use Eloquent\Phony\Stub\Answer\Builder\GeneratorAnswerBuilderFactory;
use Eloquent\Phony\Verification\GeneratorVerifierFactory;
use Eloquent\Phony\Verification\IterableVerifierFactory;
use InvalidArgumentException;

/**
 * Creates stub verifiers.
 */
class StubVerifierFactory
{
    /**
     * Get the static instance of this factory.
     *
     * @return StubVerifierFactory The static factory.
     */
    public static function instance()
    {
        if (!self::$instance) {
            self::$instance = new self(
                StubFactory::instance(),
                SpyFactory::instance(),
                MatcherFactory::instance(),
                MatcherVerifier::instance(),
                GeneratorVerifierFactory::instance(),
                IterableVerifierFactory::instance(),
                CallVerifierFactory::instance(),
                ExceptionAssertionRecorder::instance(),
                AssertionRenderer::instance(),
                GeneratorAnswerBuilderFactory::instance(),
                FunctionHookManager::instance()
            );
        }

        return self::$instance;
    }

    /**
     * Construct a new stub verifier factory.
     *
     * @param StubFactory                   $stubFactory                   The stub factory to use.
     * @param SpyFactory                    $spyFactory                    The spy factory to use.
     * @param MatcherFactory                $matcherFactory                The matcher factory to use.
     * @param MatcherVerifier               $matcherVerifier               The macther verifier to use.
     * @param GeneratorVerifierFactory      $generatorVerifierFactory      The generator verifier factory to use.
     * @param IterableVerifierFactory       $iterableVerifierFactory       The iterable verifier factory to use.
     * @param CallVerifierFactory           $callVerifierFactory           The call verifier factory to use.
     * @param AssertionRecorder             $assertionRecorder             The assertion recorder to use.
     * @param AssertionRenderer             $assertionRenderer             The assertion renderer to use.
     * @param GeneratorAnswerBuilderFactory $generatorAnswerBuilderFactory The generator answer builder factory to use.
     * @param FunctionHookManager           $functionHookManager           The function hook manager to use.
     */
    public function __construct(
        StubFactory $stubFactory,
        SpyFactory $spyFactory,
        MatcherFactory $matcherFactory,
        MatcherVerifier $matcherVerifier,
        GeneratorVerifierFactory $generatorVerifierFactory,
        IterableVerifierFactory $iterableVerifierFactory,
        CallVerifierFactory $callVerifierFactory,
        AssertionRecorder $assertionRecorder,
        AssertionRenderer $assertionRenderer,
        GeneratorAnswerBuilderFactory $generatorAnswerBuilderFactory,
        FunctionHookManager $functionHookManager
    ) {
        $this->stubFactory = $stubFactory;
        $this->spyFactory = $spyFactory;
        $this->matcherFactory = $matcherFactory;
        $this->matcherVerifier = $matcherVerifier;
        $this->generatorVerifierFactory = $generatorVerifierFactory;
        $this->iterableVerifierFactory = $iterableVerifierFactory;
        $this->callVerifierFactory = $callVerifierFactory;
        $this->assertionRecorder = $assertionRecorder;
        $this->assertionRenderer = $assertionRenderer;
        $this->generatorAnswerBuilderFactory = $generatorAnswerBuilderFactory;
        $this->functionHookManager = $functionHookManager;
    }

    /**
     * Create a new stub verifier.
     *
     * @param Stub|null $stub The stub, or null to create an anonymous stub.
     * @param Spy|null  $spy  The spy, or null to spy on the supplied stub.
     *
     * @return StubVerifier The newly created stub verifier.
     */
    public function create(Stub $stub = null, Spy $spy = null)
    {
        if (!$stub) {
            $stub = $this->stubFactory->create();
        }
        if (!$spy) {
            $spy = $this->spyFactory->create($stub);
        }

        return new StubVerifier(
            $stub,
            $spy,
            $this->matcherFactory,
            $this->matcherVerifier,
            $this->generatorVerifierFactory,
            $this->iterableVerifierFactory,
            $this->callVerifierFactory,
            $this->assertionRecorder,
            $this->assertionRenderer,
            $this->generatorAnswerBuilderFactory
        );
    }

    /**
     * Create a new stub verifier for the supplied callback.
     *
     * @param callable|null $callback The callback, or null to create an anonymous stub.
     *
     * @return StubVerifier The newly created stub verifier.
     */
    public function createFromCallback($callback = null)
    {
        $stub = $this->stubFactory->create($callback);

        return new StubVerifier(
            $stub,
            $this->spyFactory->create($stub),
            $this->matcherFactory,
            $this->matcherVerifier,
            $this->generatorVerifierFactory,
            $this->iterableVerifierFactory,
            $this->callVerifierFactory,
            $this->assertionRecorder,
            $this->assertionRenderer,
            $this->generatorAnswerBuilderFactory
        );
    }

    /**
     * Create a new stub verifier for a global function and declare it in the
     * specified namespace.
     *
     * @param string $function  The function name.
     * @param string $namespace The namespace.
     *
     * @return StubVerifier             The newly created stub verifier.
     * @throws InvalidArgumentException If an invalid function name or namespace is specified.
     */
    public function createGlobal($function, $namespace)
    {
        if (false !== strpos($function, '\\')) {
            throw new InvalidArgumentException(
                'Only functions in the global namespace are supported.'
            );
        }

        $namespace = trim($namespace, '\\');

        if (!$namespace) {
            throw new InvalidArgumentException(
                'The supplied namespace must not be empty.'
            );
        }

        $stub = $this->stubFactory->create($function);
        $spy = $this->spyFactory->create($stub);
        $this->functionHookManager->defineFunction($function, $namespace, $spy);

        return new StubVerifier(
            $stub,
            $spy,
            $this->matcherFactory,
            $this->matcherVerifier,
            $this->generatorVerifierFactory,
            $this->iterableVerifierFactory,
            $this->callVerifierFactory,
            $this->assertionRecorder,
            $this->assertionRenderer,
            $this->generatorAnswerBuilderFactory
        );
    }

    private static $instance;
    private $stubFactory;
    private $spyFactory;
    private $matcherFactory;
    private $matcherVerifier;
    private $generatorVerifierFactory;
    private $iterableVerifierFactory;
    private $callVerifierFactory;
    private $assertionRecorder;
    private $assertionRenderer;
    private $generatorAnswerBuilderFactory;
    private $functionHookManager;
}

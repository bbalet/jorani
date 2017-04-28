<?php

/*
 * This file is part of the Phony package.
 *
 * Copyright © 2016 Erin Millard
 *
 * For the full copyright and license information, please view the LICENSE file
 * that was distributed with this source code.
 */

namespace Eloquent\Phony\Spy;

use Eloquent\Phony\Assertion\AssertionRecorder;
use Eloquent\Phony\Assertion\AssertionRenderer;
use Eloquent\Phony\Assertion\ExceptionAssertionRecorder;
use Eloquent\Phony\Call\CallVerifierFactory;
use Eloquent\Phony\Hook\FunctionHookManager;
use Eloquent\Phony\Matcher\MatcherFactory;
use Eloquent\Phony\Matcher\MatcherVerifier;
use Eloquent\Phony\Verification\GeneratorVerifierFactory;
use Eloquent\Phony\Verification\IterableVerifierFactory;
use InvalidArgumentException;

/**
 * Creates spy verifiers.
 */
class SpyVerifierFactory
{
    /**
     * Get the static instance of this factory.
     *
     * @return SpyVerifierFactory The static factory.
     */
    public static function instance()
    {
        if (!self::$instance) {
            self::$instance = new self(
                SpyFactory::instance(),
                MatcherFactory::instance(),
                MatcherVerifier::instance(),
                GeneratorVerifierFactory::instance(),
                IterableVerifierFactory::instance(),
                CallVerifierFactory::instance(),
                ExceptionAssertionRecorder::instance(),
                AssertionRenderer::instance(),
                FunctionHookManager::instance()
            );
        }

        return self::$instance;
    }

    /**
     * Construct a new spy verifier factory.
     *
     * @param SpyFactory               $spyFactory               The spy factory to use.
     * @param MatcherFactory           $matcherFactory           The matcher factory to use.
     * @param MatcherVerifier          $matcherVerifier          The macther verifier to use.
     * @param GeneratorVerifierFactory $generatorVerifierFactory The generator verifier factory to use.
     * @param IterableVerifierFactory  $iterableVerifierFactory  The iterable verifier factory to use.
     * @param CallVerifierFactory      $callVerifierFactory      The call verifier factory to use.
     * @param AssertionRecorder        $assertionRecorder        The assertion recorder to use.
     * @param AssertionRenderer        $assertionRenderer        The assertion renderer to use.
     * @param FunctionHookManager      $functionHookManager      The function hook manager to use.
     */
    public function __construct(
        SpyFactory $spyFactory,
        MatcherFactory $matcherFactory,
        MatcherVerifier $matcherVerifier,
        GeneratorVerifierFactory $generatorVerifierFactory,
        IterableVerifierFactory $iterableVerifierFactory,
        CallVerifierFactory $callVerifierFactory,
        AssertionRecorder $assertionRecorder,
        AssertionRenderer $assertionRenderer,
        FunctionHookManager $functionHookManager
    ) {
        $this->spyFactory = $spyFactory;
        $this->matcherFactory = $matcherFactory;
        $this->matcherVerifier = $matcherVerifier;
        $this->generatorVerifierFactory = $generatorVerifierFactory;
        $this->iterableVerifierFactory = $iterableVerifierFactory;
        $this->callVerifierFactory = $callVerifierFactory;
        $this->assertionRecorder = $assertionRecorder;
        $this->assertionRenderer = $assertionRenderer;
        $this->functionHookManager = $functionHookManager;
    }

    /**
     * Create a new spy verifier.
     *
     * @param Spy|null $spy The spy, or null to create an anonymous spy.
     *
     * @return SpyVerifier The newly created spy verifier.
     */
    public function create(Spy $spy = null)
    {
        if (!$spy) {
            $spy = $this->spyFactory->create();
        }

        return new SpyVerifier(
            $spy,
            $this->matcherFactory,
            $this->matcherVerifier,
            $this->generatorVerifierFactory,
            $this->iterableVerifierFactory,
            $this->callVerifierFactory,
            $this->assertionRecorder,
            $this->assertionRenderer
        );
    }

    /**
     * Create a new spy verifier for the supplied callback.
     *
     * @param callable|null $callback The callback, or null to create an anonymous spy.
     *
     * @return SpyVerifier The newly created spy verifier.
     */
    public function createFromCallback($callback = null)
    {
        return new SpyVerifier(
            $this->spyFactory->create($callback),
            $this->matcherFactory,
            $this->matcherVerifier,
            $this->generatorVerifierFactory,
            $this->iterableVerifierFactory,
            $this->callVerifierFactory,
            $this->assertionRecorder,
            $this->assertionRenderer
        );
    }

    /**
     * Create a new spy verifier for a global function and declare it in the
     * specified namespace.
     *
     * @param string $function  The function name.
     * @param string $namespace The namespace.
     *
     * @return SpyVerifier              The newly created spy verifier.
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

        $spy = $this->spyFactory->create($function);
        $this->functionHookManager->defineFunction($function, $namespace, $spy);

        return new SpyVerifier(
            $spy,
            $this->matcherFactory,
            $this->matcherVerifier,
            $this->generatorVerifierFactory,
            $this->iterableVerifierFactory,
            $this->callVerifierFactory,
            $this->assertionRecorder,
            $this->assertionRenderer
        );
    }

    private static $instance;
    private $spyFactory;
    private $matcherFactory;
    private $matcherVerifier;
    private $generatorVerifierFactory;
    private $iterableVerifierFactory;
    private $callVerifierFactory;
    private $assertionRecorder;
    private $assertionRenderer;
    private $functionHookManager;
}

<?php

/*
 * This file is part of the Phony package.
 *
 * Copyright © 2016 Erin Millard
 *
 * For the full copyright and license information, please view the LICENSE file
 * that was distributed with this source code.
 */

namespace Eloquent\Phony\Facade;

use Eloquent\Phony\Assertion\AssertionRecorder;
use Eloquent\Phony\Assertion\AssertionRenderer;
use Eloquent\Phony\Assertion\ExceptionAssertionRecorder;
use Eloquent\Phony\Call\CallFactory;
use Eloquent\Phony\Call\CallVerifierFactory;
use Eloquent\Phony\Call\Event\CallEventFactory;
use Eloquent\Phony\Clock\SystemClock;
use Eloquent\Phony\Difference\DifferenceEngine;
use Eloquent\Phony\Event\EventOrderVerifier;
use Eloquent\Phony\Exporter\InlineExporter;
use Eloquent\Phony\Hook\FunctionHookGenerator;
use Eloquent\Phony\Hook\FunctionHookManager;
use Eloquent\Phony\Integration\CounterpartMatcherDriver;
use Eloquent\Phony\Integration\HamcrestMatcherDriver;
use Eloquent\Phony\Integration\MockeryMatcherDriver;
use Eloquent\Phony\Integration\PhakeMatcherDriver;
use Eloquent\Phony\Integration\ProphecyMatcherDriver;
use Eloquent\Phony\Invocation\InvocableInspector;
use Eloquent\Phony\Invocation\Invoker;
use Eloquent\Phony\Matcher\AnyMatcher;
use Eloquent\Phony\Matcher\MatcherFactory;
use Eloquent\Phony\Matcher\MatcherVerifier;
use Eloquent\Phony\Matcher\WildcardMatcher;
use Eloquent\Phony\Mock\Builder\MockBuilderFactory;
use Eloquent\Phony\Mock\Handle\HandleFactory;
use Eloquent\Phony\Mock\MockFactory;
use Eloquent\Phony\Mock\MockGenerator;
use Eloquent\Phony\Phpunit\PhpunitMatcherDriver;
use Eloquent\Phony\Reflection\FeatureDetector;
use Eloquent\Phony\Reflection\HhvmFunctionSignatureInspector;
use Eloquent\Phony\Reflection\PhpFunctionSignatureInspector;
use Eloquent\Phony\Sequencer\Sequencer;
use Eloquent\Phony\Simpletest\SimpletestMatcherDriver;
use Eloquent\Phony\Spy\GeneratorSpyFactory;
use Eloquent\Phony\Spy\IterableSpyFactory;
use Eloquent\Phony\Spy\SpyFactory;
use Eloquent\Phony\Spy\SpyVerifierFactory;
use Eloquent\Phony\Stub\Answer\Builder\GeneratorAnswerBuilderFactory;
use Eloquent\Phony\Stub\EmptyValueFactory;
use Eloquent\Phony\Stub\StubFactory;
use Eloquent\Phony\Stub\StubVerifierFactory;
use Eloquent\Phony\Verification\GeneratorVerifierFactory;
use Eloquent\Phony\Verification\IterableVerifierFactory;

/**
 * A service container that supplies all of the underlying services required by
 * the facades.
 */
class FacadeDriver
{
    /**
     * Get the static instance of this driver.
     *
     * @return FacadeDriver The static driver.
     */
    public static function instance()
    {
        if (!self::$instance) {
            self::$instance = new self(ExceptionAssertionRecorder::instance());
        }

        return self::$instance;
    }

    /**
     * Construct a new facade driver.
     *
     * @param AssertionRecorder $assertionRecorder The assertion recorder to use.
     */
    protected function __construct(AssertionRecorder $assertionRecorder)
    {
        $this->sequences = array();

        $anyMatcher = new AnyMatcher();
        $objectIdSequence = Sequencer::sequence('exporter-object-id');
        $invocableInspector = new InvocableInspector();
        $exporter = new InlineExporter(
            1,
            $objectIdSequence,
            $invocableInspector
        );
        $featureDetector = new FeatureDetector();
        $invoker = new Invoker();
        $matcherVerifier = new MatcherVerifier();

        if ($featureDetector->isSupported('runtime.hhvm')) {
            // @codeCoverageIgnoreStart
            $functionSignatureInspector = new HhvmFunctionSignatureInspector(
                $invocableInspector,
                $featureDetector
            );
            // @codeCoverageIgnoreEnd
        } else {
            $functionSignatureInspector = new PhpFunctionSignatureInspector(
                $invocableInspector,
                $featureDetector
            );
        }

        $mockClassLabelSequence = Sequencer::sequence('mock-class-label');
        $this->sequences[] = $mockClassLabelSequence;
        $mockGenerator = new MockGenerator(
            $mockClassLabelSequence,
            $functionSignatureInspector,
            $featureDetector
        );
        $wildcardMatcher = new WildcardMatcher(
            $anyMatcher,
            0,
            null
        );
        $matcherFactory = new MatcherFactory(
            $anyMatcher,
            $wildcardMatcher,
            $exporter
        );
        $matcherFactory->addMatcherDriver(new HamcrestMatcherDriver());
        $matcherFactory->addMatcherDriver(new CounterpartMatcherDriver());
        $matcherFactory->addMatcherDriver(new PhpunitMatcherDriver());
        $matcherFactory->addMatcherDriver(new SimpletestMatcherDriver());
        $matcherFactory
            ->addMatcherDriver(new PhakeMatcherDriver($wildcardMatcher));
        $matcherFactory
            ->addMatcherDriver(new ProphecyMatcherDriver($wildcardMatcher));
        $matcherFactory->addMatcherDriver(new MockeryMatcherDriver());
        $emptyValueFactory = new EmptyValueFactory(
            $featureDetector
        );
        $generatorAnswerBuilderFactory = new GeneratorAnswerBuilderFactory(
            $invocableInspector,
            $invoker,
            $featureDetector
        );
        $stubLabelSequence = Sequencer::sequence('stub-label');
        $this->sequences[] = $stubLabelSequence;
        $stubFactory = new StubFactory(
            $stubLabelSequence,
            $matcherFactory,
            $matcherVerifier,
            $invoker,
            $invocableInspector,
            $emptyValueFactory,
            $generatorAnswerBuilderFactory
        );
        $clock = new SystemClock('microtime');
        $eventSequence = Sequencer::sequence('event-sequence-number');
        $this->sequences[] = $eventSequence;
        $eventFactory = new CallEventFactory(
            $eventSequence,
            $clock
        );
        $callFactory = new CallFactory(
            $eventFactory,
            $invoker
        );
        $generatorSpyFactory = new GeneratorSpyFactory(
            $eventFactory,
            $featureDetector
        );
        $iterableSpyFactory = new IterableSpyFactory(
            $eventFactory
        );
        $spyLabelSequence = Sequencer::sequence('spy-label');
        $this->sequences[] = $spyLabelSequence;
        $spyFactory = new SpyFactory(
            $spyLabelSequence,
            $callFactory,
            $invoker,
            $generatorSpyFactory,
            $iterableSpyFactory
        );
        $differenceEngine = new DifferenceEngine(
            $featureDetector
        );
        $assertionRenderer = new AssertionRenderer(
            $matcherVerifier,
            $exporter,
            $differenceEngine,
            $featureDetector
        );
        $generatorVerifierFactory = new GeneratorVerifierFactory(
            $matcherFactory,
            $assertionRecorder,
            $assertionRenderer
        );
        $iterableVerifierFactory = new IterableVerifierFactory(
            $matcherFactory,
            $assertionRecorder,
            $assertionRenderer
        );
        $callVerifierFactory = new CallVerifierFactory(
            $matcherFactory,
            $matcherVerifier,
            $generatorVerifierFactory,
            $iterableVerifierFactory,
            $assertionRecorder,
            $assertionRenderer
        );
        $assertionRecorder->setCallVerifierFactory($callVerifierFactory);
        $functionHookGenerator = new FunctionHookGenerator();
        $functionHookManager = new FunctionHookManager(
            $functionSignatureInspector,
            $functionHookGenerator
        );
        $stubVerifierFactory = new StubVerifierFactory(
            $stubFactory,
            $spyFactory,
            $matcherFactory,
            $matcherVerifier,
            $generatorVerifierFactory,
            $iterableVerifierFactory,
            $callVerifierFactory,
            $assertionRecorder,
            $assertionRenderer,
            $generatorAnswerBuilderFactory,
            $functionHookManager
        );
        $handleFactory = new HandleFactory(
            $stubFactory,
            $stubVerifierFactory,
            $emptyValueFactory,
            $assertionRenderer,
            $assertionRecorder,
            $invoker
        );
        $mockLabelSequence = Sequencer::sequence('mock-label');
        $this->sequences[] = $mockLabelSequence;
        $mockFactory = new MockFactory(
            $mockLabelSequence,
            $mockGenerator,
            $handleFactory,
            $featureDetector
        );
        $mockBuilderFactory = new MockBuilderFactory(
            $mockFactory,
            $handleFactory,
            $invocableInspector,
            $featureDetector
        );
        $spyVerifierFactory = new SpyVerifierFactory(
            $spyFactory,
            $matcherFactory,
            $matcherVerifier,
            $generatorVerifierFactory,
            $iterableVerifierFactory,
            $callVerifierFactory,
            $assertionRecorder,
            $assertionRenderer,
            $functionHookManager
        );
        $eventOrderVerifier = new EventOrderVerifier(
            $assertionRecorder,
            $assertionRenderer
        );

        $emptyValueFactory->setStubVerifierFactory($stubVerifierFactory);
        $emptyValueFactory->setMockBuilderFactory($mockBuilderFactory);
        $generatorVerifierFactory->setCallVerifierFactory($callVerifierFactory);
        $iterableVerifierFactory
            ->setCallVerifierFactory($callVerifierFactory);

        $this->mockBuilderFactory = $mockBuilderFactory;
        $this->handleFactory = $handleFactory;
        $this->spyVerifierFactory = $spyVerifierFactory;
        $this->stubVerifierFactory = $stubVerifierFactory;
        $this->functionHookManager = $functionHookManager;
        $this->eventOrderVerifier = $eventOrderVerifier;
        $this->matcherFactory = $matcherFactory;
        $this->exporter = $exporter;
        $this->assertionRenderer = $assertionRenderer;
        $this->differenceEngine = $differenceEngine;
    }

    public $mockBuilderFactory;
    public $handleFactory;
    public $spyVerifierFactory;
    public $stubVerifierFactory;
    public $functionHookManager;
    public $eventOrderVerifier;
    public $matcherFactory;
    public $exporter;
    public $assertionRenderer;
    public $differenceEngine;
    protected $sequences;
    private static $instance;
}

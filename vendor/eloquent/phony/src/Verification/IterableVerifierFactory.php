<?php

/*
 * This file is part of the Phony package.
 *
 * Copyright © 2016 Erin Millard
 *
 * For the full copyright and license information, please view the LICENSE file
 * that was distributed with this source code.
 */

namespace Eloquent\Phony\Verification;

use Eloquent\Phony\Assertion\AssertionRecorder;
use Eloquent\Phony\Assertion\AssertionRenderer;
use Eloquent\Phony\Assertion\ExceptionAssertionRecorder;
use Eloquent\Phony\Call\Call;
use Eloquent\Phony\Call\CallVerifierFactory;
use Eloquent\Phony\Matcher\MatcherFactory;
use Eloquent\Phony\Spy\Spy;

/**
 * Creates iterable verifiers.
 */
class IterableVerifierFactory
{
    /**
     * Get the static instance of this factory.
     *
     * @return IterableVerifierFactory The static factory.
     */
    public static function instance()
    {
        if (!self::$instance) {
            self::$instance = new self(
                MatcherFactory::instance(),
                ExceptionAssertionRecorder::instance(),
                AssertionRenderer::instance()
            );
        }

        return self::$instance;
    }

    /**
     * Construct a new event order verifier factory.
     *
     * @param MatcherFactory    $matcherFactory    The matcher factory to use.
     * @param AssertionRecorder $assertionRecorder The assertion recorder to use.
     * @param AssertionRenderer $assertionRenderer The assertion renderer to use.
     */
    public function __construct(
        MatcherFactory $matcherFactory,
        AssertionRecorder $assertionRecorder,
        AssertionRenderer $assertionRenderer
    ) {
        $this->matcherFactory = $matcherFactory;
        $this->assertionRecorder = $assertionRecorder;
        $this->assertionRenderer = $assertionRenderer;
    }

    /**
     * Set the call verifier factory.
     *
     * @param CallVerifierFactory $callVerifierFactory The call verifier factory to use.
     */
    public function setCallVerifierFactory(
        CallVerifierFactory $callVerifierFactory
    ) {
        $this->callVerifierFactory = $callVerifierFactory;
    }

    /**
     * Create a new iterable verifier.
     *
     * @param Spy|Call    $subject The subject.
     * @param array<Call> $calls   The calls.
     *
     * @return IterableVerifier The newly created iterable verifier.
     */
    public function create($subject, array $calls)
    {
        return new IterableVerifier(
            $subject,
            $calls,
            $this->matcherFactory,
            $this->callVerifierFactory,
            $this->assertionRecorder,
            $this->assertionRenderer
        );
    }

    private static $instance;
    private $matcherFactory;
    private $assertionRecorder;
    private $assertionRenderer;
    private $callVerifierFactory;
}

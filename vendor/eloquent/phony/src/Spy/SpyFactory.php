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

use Eloquent\Phony\Call\CallFactory;
use Eloquent\Phony\Invocation\Invoker;
use Eloquent\Phony\Sequencer\Sequencer;

/**
 * Creates spies.
 */
class SpyFactory
{
    /**
     * Get the static instance of this factory.
     *
     * @return SpyFactory The static factory.
     */
    public static function instance()
    {
        if (!self::$instance) {
            self::$instance = new self(
                Sequencer::sequence('spy-label'),
                CallFactory::instance(),
                Invoker::instance(),
                GeneratorSpyFactory::instance(),
                IterableSpyFactory::instance()
            );
        }

        return self::$instance;
    }

    /**
     * Construct a new spy factory.
     *
     * @param Sequencer           $labelSequencer      The label sequencer to use.
     * @param CallFactory         $callFactory         The call factory to use.
     * @param Invoker             $invoker             The invoker to use.
     * @param GeneratorSpyFactory $generatorSpyFactory The generator spy factory to use.
     * @param IterableSpyFactory  $iterableSpyFactory  The iterable spy factory to use.
     */
    public function __construct(
        Sequencer $labelSequencer,
        CallFactory $callFactory,
        Invoker $invoker,
        GeneratorSpyFactory $generatorSpyFactory,
        IterableSpyFactory $iterableSpyFactory
    ) {
        $this->labelSequencer = $labelSequencer;
        $this->callFactory = $callFactory;
        $this->invoker = $invoker;
        $this->generatorSpyFactory = $generatorSpyFactory;
        $this->iterableSpyFactory = $iterableSpyFactory;
    }

    /**
     * Create a new spy.
     *
     * @param callable|null $callback The callback, or null to create an anonymous spy.
     *
     * @return Spy The newly created spy.
     */
    public function create($callback = null)
    {
        return new SpyData(
            $callback,
            strval($this->labelSequencer->next()),
            $this->callFactory,
            $this->invoker,
            $this->generatorSpyFactory,
            $this->iterableSpyFactory
        );
    }

    private static $instance;
    private $labelSequencer;
    private $callFactory;
    private $invoker;
    private $generatorSpyFactory;
    private $iterableSpyFactory;
}

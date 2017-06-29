<?php

/*
 * This file is part of the Phony package.
 *
 * Copyright © 2016 Erin Millard
 *
 * For the full copyright and license information, please view the LICENSE file
 * that was distributed with this source code.
 */

namespace Eloquent\Phony\Call;

use Eloquent\Phony\Call\Event\CallEventFactory;
use Eloquent\Phony\Invocation\Invoker;
use Eloquent\Phony\Spy\SpyData;
use Exception;
use Throwable;

/**
 * Creates calls.
 */
class CallFactory
{
    /**
     * Get the static instance of this factory.
     *
     * @return CallFactory The static factory.
     */
    public static function instance()
    {
        if (!self::$instance) {
            self::$instance = new self(
                CallEventFactory::instance(),
                Invoker::instance()
            );
        }

        return self::$instance;
    }

    /**
     * Construct a new call factory.
     *
     * @param CallEventFactory $eventFactory The call event factory to use.
     * @param Invoker          $invoker      The invoker to use.
     */
    public function __construct(
        CallEventFactory $eventFactory,
        Invoker $invoker
    ) {
        $this->eventFactory = $eventFactory;
        $this->invoker = $invoker;
    }

    /**
     * Record call details by invoking a callback.
     *
     * @param callable  $callback  The callback.
     * @param Arguments $arguments The arguments.
     * @param SpyData   $spy       The spy to record the call to.
     *
     * @return CallData The newly created call.
     */
    public function record(
        $callback,
        Arguments $arguments,
        SpyData $spy
    ) {
        $originalArguments = $arguments->copy();

        $call = new CallData(
            $spy->nextIndex(),
            $this->eventFactory->createCalled($spy, $originalArguments)
        );
        $spy->addCall($call);

        $returnValue = null;
        $exception = null;

        try {
            $returnValue = $this->invoker->callWith($callback, $arguments);
        } catch (Throwable $exception) {
            // @codeCoverageIgnoreStart
        } catch (Exception $exception) {
        }
        // @codeCoverageIgnoreEnd

        if ($exception) {
            $responseEvent = $this->eventFactory->createThrew($exception);
        } else {
            $responseEvent = $this->eventFactory->createReturned($returnValue);
        }

        $call->setResponseEvent($responseEvent);

        return $call;
    }

    private static $instance;
    private $eventFactory;
    private $invoker;
}

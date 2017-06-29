<?php

/*
 * This file is part of the Phony package.
 *
 * Copyright © 2016 Erin Millard
 *
 * For the full copyright and license information, please view the LICENSE file
 * that was distributed with this source code.
 */

namespace Eloquent\Phony\Simpletest;

use Eloquent\Phony\Facade\FacadeDriver;
use SimpleTest;

/**
 * A facade driver for SimpleTest.
 *
 * @codeCoverageIgnore
 */
class SimpletestFacadeDriver extends FacadeDriver
{
    /**
     * Get the static instance of this driver.
     *
     * @return FacadeDriver The static driver.
     */
    public static function instance()
    {
        if (!self::$instance) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * Construct a new SimpleTest facade driver.
     */
    public function __construct()
    {
        parent::__construct(
            new SimpletestAssertionRecorder(SimpleTest::getContext())
        );
    }

    private static $instance;
}

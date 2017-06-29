<?php

/*
 * This file is part of the Phony package.
 *
 * Copyright © 2016 Erin Millard
 *
 * For the full copyright and license information, please view the LICENSE file
 * that was distributed with this source code.
 */

namespace Eloquent\Phony\Invocation;

/**
 * An abstract base class for implementing wrapped invocables.
 */
abstract class AbstractWrappedInvocable extends AbstractInvocable implements
    WrappedInvocable
{
    /**
     * Construct a new wrapped invocable.
     *
     * @param callable|null $callback The callback.
     * @param string|null   $label    The label.
     */
    public function __construct($callback, $label)
    {
        if (!$callback) {
            $this->isAnonymous = true;
            $this->callback = function () {};
        } else {
            $this->isAnonymous = false;
            $this->callback = $callback;
        }

        $this->label = $label;
    }

    /**
     * Returns true if anonymous.
     *
     * @return bool True if anonymous.
     */
    public function isAnonymous()
    {
        return $this->isAnonymous;
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
     * Set the label.
     *
     * @param string|null $label The label.
     *
     * @return $this This invocable.
     */
    public function setLabel($label)
    {
        $this->label = $label;

        return $this;
    }

    /**
     * Get the label.
     *
     * @return string|null The label.
     */
    public function label()
    {
        return $this->label;
    }

    protected $isAnonymous;
    protected $callback;
    protected $label;
}

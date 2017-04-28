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
 * The interface implemented by wrapped invocables.
 */
interface WrappedInvocable extends Invocable
{
    /**
     * Set the label.
     *
     * @param string|null $label The label.
     *
     * @return $this This invocable.
     */
    public function setLabel($label);

    /**
     * Get the label.
     *
     * @return string|null The label.
     */
    public function label();

    /**
     * Returns true if anonymous.
     *
     * @return bool True if anonymous.
     */
    public function isAnonymous();

    /**
     * Get the callback.
     *
     * @return callable The callback.
     */
    public function callback();
}

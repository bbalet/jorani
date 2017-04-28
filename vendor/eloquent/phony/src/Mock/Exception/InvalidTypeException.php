<?php

/*
 * This file is part of the Phony package.
 *
 * Copyright © 2016 Erin Millard
 *
 * For the full copyright and license information, please view the LICENSE file
 * that was distributed with this source code.
 */

namespace Eloquent\Phony\Mock\Exception;

use Exception;

/**
 * Unable to add the supplied type.
 */
final class InvalidTypeException extends Exception implements
    MockException
{
    /**
     * Construct a new invalid type exception.
     *
     * @param mixed          $type  The type.
     * @param Exception|null $cause The cause, if available.
     */
    public function __construct($type, Exception $cause = null)
    {
        $this->type = $type;

        if (is_string($type)) {
            $message = sprintf('Undefined type %s.', var_export($type, true));
        } else {
            $message = sprintf(
                'Unable to add type of type %s.',
                var_export(gettype($type), true)
            );
        }

        parent::__construct($message, 0, $cause);
    }

    /**
     * Get the type.
     *
     * @return mixed The type.
     */
    public function type()
    {
        return $this->type;
    }

    private $type;
}

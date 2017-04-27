<?php

/*
 * This file is part of the Phony package.
 *
 * Copyright © 2016 Erin Millard
 *
 * For the full copyright and license information, please view the LICENSE file
 * that was distributed with this source code.
 */

namespace Eloquent\Phony\Stub\Answer;

/**
 * Represents a stub answer.
 */
class Answer
{
    /**
     * Construct a new answer.
     *
     * @param CallRequest        $primaryRequest    The primary request.
     * @param array<CallRequest> $secondaryRequests The secondary requests.
     */
    public function __construct(
        CallRequest $primaryRequest,
        array $secondaryRequests
    ) {
        $this->primaryRequest = $primaryRequest;
        $this->secondaryRequests = $secondaryRequests;
    }

    /**
     * Get the primary request.
     *
     * @return CallRequest The primary request.
     */
    public function primaryRequest()
    {
        return $this->primaryRequest;
    }

    /**
     * Get the secondary requests.
     *
     * @return array<CallRequest> The secondary requests.
     */
    public function secondaryRequests()
    {
        return $this->secondaryRequests;
    }

    private $primaryRequest;
    private $secondaryRequests;
}

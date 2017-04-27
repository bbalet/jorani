<?php

/*
 * This file is part of the Phony package.
 *
 * Copyright © 2016 Erin Millard
 *
 * For the full copyright and license information, please view the LICENSE file
 * that was distributed with this source code.
 */

namespace Eloquent\Phony\Stub;

use Eloquent\Phony\Matcher\Matchable;
use Eloquent\Phony\Stub\Answer\Answer;
use Eloquent\Phony\Stub\Exception\UndefinedAnswerException;

/**
 * Represents a set of criteria and associated answers.
 */
class StubRule
{
    /**
     * Construct a new stub rule.
     *
     * @param array<Matchable> $criteria The criteria.
     * @param array<Answer>    $answers  The answers.
     */
    public function __construct(array $criteria, array $answers)
    {
        $this->criteria = $criteria;
        $this->answers = $answers;

        $this->lastIndex = count($answers) - 1;
        $this->calledCount = 0;
    }

    /**
     * Get the criteria.
     *
     * @return array<Matchable> The criteria.
     */
    public function criteria()
    {
        return $this->criteria;
    }

    /**
     * Get the answers.
     *
     * @return array<Answer> The answers.
     */
    public function answers()
    {
        return $this->answers;
    }

    /**
     * Get the next answer.
     *
     * @return Answer                   The answer.
     * @throws UndefinedAnswerException If an undefined or incomplete answer is encountered.
     */
    public function next()
    {
        if ($this->calledCount > $this->lastIndex) {
            $index = $this->lastIndex;
        } else {
            $index = $this->calledCount;
        }

        ++$this->calledCount;

        if (
            !isset($this->answers[$index]) ||
            !$this->answers[$index]->primaryRequest()
        ) {
            throw new UndefinedAnswerException();
        }

        return $this->answers[$index];
    }

    private $criteria;
    private $answers;
    private $lastIndex;
    private $calledCount;
}

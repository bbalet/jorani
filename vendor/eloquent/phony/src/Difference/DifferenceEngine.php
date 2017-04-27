<?php

/*
 * This file is part of the Phony package.
 *
 * Copyright © 2016 Erin Millard
 *
 * For the full copyright and license information, please view the LICENSE file
 * that was distributed with this source code.
 */

namespace Eloquent\Phony\Difference;

use Eloquent\Phony\Reflection\FeatureDetector;

/**
 * Calculates differences in values.
 */
class DifferenceEngine
{
    /**
     * Get the static instance of this engine.
     *
     * @return DifferenceEngine The static engine.
     */
    public static function instance()
    {
        if (!self::$instance) {
            self::$instance = new self(FeatureDetector::instance());
        }

        return self::$instance;
    }

    /**
     * Construct a new difference engine.
     *
     * @param FeatureDetector $featureDetector The feature detector to use.
     */
    public function __construct(FeatureDetector $featureDetector)
    {
        $this->featureDetector = $featureDetector;

        $this->setUseColor(null);
    }

    /**
     * Turn on or off the use of ANSI colored output.
     *
     * Pass `null` to detect automatically.
     *
     * @param bool|null $useColor True to use color.
     */
    public function setUseColor($useColor)
    {
        if (null === $useColor) {
            $useColor = $this->featureDetector->isSupported('stdout.ansi');
        }

        // @codeCoverageIgnoreStart
        if ($useColor) {
            $this->addStart = "\033[33m\033[2m{+\033[0m\033[33m\033[4m";
            $this->addEnd = "\033[0m\033[33m\033[2m+}\033[0m";
            $this->removeStart = "\033[36m\033[2m[-\033[0m\033[36m\033[4m";
            $this->removeEnd = "\033[0m\033[36m\033[2m-]\033[0m";
        } else {
            // @codeCoverageIgnoreEnd
            $this->addStart = '{+';
            $this->addEnd = '+}';
            $this->removeStart = '[-';
            $this->removeEnd = '-]';
        }
    }

    /**
     * Get the difference between the supplied strings.
     *
     * @param string $from The from value.
     * @param string $to   The to value.
     *
     * @return string The difference.
     */
    public function difference($from, $to)
    {
        $flags = PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY;
        $from = preg_split('/(\W+)/u', $from, -1, $flags);
        $to = preg_split('/(\W+)/u', $to, -1, $flags);

        $matcher = new DifferenceSequenceMatcher($from, $to);
        $diff = '';

        foreach ($matcher->getOpcodes() as $opcode) {
            list($tag, $i1, $i2, $j1, $j2) = $opcode;

            if ($tag === 'equal') {
                $diff .= implode(array_slice($from, $i1, $i2 - $i1));
            } else {
                if ($tag === 'replace' || $tag === 'delete') {
                    $diff .=
                        $this->removeStart .
                        implode(array_slice($from, $i1, $i2 - $i1)) .
                        $this->removeEnd;
                }

                if ($tag === 'replace' || $tag === 'insert') {
                    $diff .=
                        $this->addStart .
                        implode(array_slice($to, $j1, $j2 - $j1)) .
                        $this->addEnd;
                }
            }
        }

        return $diff;
    }

    private static $instance;
    private $featureDetector;
    private $addStart;
    private $addEnd;
    private $removeStart;
    private $removeEnd;
}

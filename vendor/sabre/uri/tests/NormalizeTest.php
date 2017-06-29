<?php

namespace Sabre\Uri;

class NormalizeTest extends \PHPUnit_Framework_TestCase{

    /**
     * @dataProvider normalizeData
     */
    function testNormalize($in, $out) {

        $this->assertEquals(
            $out,
            normalize($in)
        );

    }

    function normalizeData() {

        return [
            [ 'http://example.org/',             'http://example.org/' ],
            [ 'HTTP://www.EXAMPLE.com/',         'http://www.example.com/'],
            [ 'http://example.org/%7Eevert',     'http://example.org/~evert'],
            [ 'http://example.org/./evert',      'http://example.org/evert'],
            [ 'http://example.org/../evert',     'http://example.org/evert'],
            [ 'http://example.org/foo/../evert', 'http://example.org/evert'],
            [ '/%41',                            '/A'],
            [ '/%3F',                            '/%3F'],
            [ '/%3f',                            '/%3F'],
            [ 'http://example.org',              'http://example.org/'],
            [ 'http://example.org:/',            'http://example.org/'],
            [ 'http://example.org:80/',          'http://example.org/'],

        ];

    }

}

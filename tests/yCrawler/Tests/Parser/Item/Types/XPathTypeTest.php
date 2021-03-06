<?php

namespace yCrawler\Tests\Parser\Item\Types;

use yCrawler\Parser\Item\Types;
use yCrawler\Tests\Testcase;

class XPathTypeTest extends Type
{
    const TESTED_CLASS = 'yCrawler\Parser\Item\Types\XPathType';

    const EXAMPLE_PATTERN_INPUT = '/foo/';
    const EXAMPLE_PATTERN_OUTPUT = '/foo/';
    const EXAMPLE_RESULT = 'foo';

    protected function createDocumentMock()
    {
        $node = (object) ['nodeValue' => static::EXAMPLE_RESULT];

        $xpath = $this->createXPathMock();
        $xpath->shouldReceive('evaluate')
            ->with(static::EXAMPLE_PATTERN_OUTPUT)
            ->once()
            ->andReturn([$node]);

        $document = parent::createDocumentMock();
        $document->shouldReceive('getXPath')
            ->withNoArgs()
            ->once()
            ->andReturn($xpath);

        $document->shouldReceive('getDOM')
            ->withNoArgs()
            ->once()
            ->andReturn(new \DOMDocument());

        return $document;
    }
}

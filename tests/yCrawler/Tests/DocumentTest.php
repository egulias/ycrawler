<?php

namespace yCrawler\Tests;

use yCrawler\Document;
use Mockery as m;
use yCrawler\Parser\Rule\XPath;

class DocumentTest extends TestCase
{
    const EXAMPLE_URL = 'http://httpbin.org/';
    const EXAMPLE_MARKUP = '<html><body><pre><a href="foo">bar</a></pre></body></html>';
    const TWO_LINKS_MARKUP = '<html><body><pre><a href="foo">bar</a><a href="foo">cad</a></pre></body></html>';

    public function createDocument($url)
    {
        $parser = $this->createParserMock();

        return new Document($url, $parser);
    }

    public function testGetURL()
    {
        $doc = $this->createDocument(self::EXAMPLE_URL);

        $this->assertSame(self::EXAMPLE_URL, $doc->getURL());
    }

    public function testGetParser()
    {
        $doc = $this->createDocument(self::EXAMPLE_URL);

        $this->assertInstanceOf('yCrawler\Mocks\ParserMock', $doc->getParser());
    }

    public function testGetMarkup()
    {
        $doc = $this->createDocument(self::EXAMPLE_URL);

        $doc->setMarkup('foo');
        $this->assertSame('foo', $doc->getMarkup());
    }

    public function testGetDOM()
    {
        $doc = $this->createDocument(self::EXAMPLE_URL);
        $doc->setMarkup(self::EXAMPLE_MARKUP);
        $doc->parse();

        $this->assertInstanceOf('DOMDocument', $doc->getDOM());
        $this->assertTrue(strlen($doc->getDOM()->saveHTML()) > 0);
    }

    public function testGetXPath()
    {
        $doc = $this->createDocument(self::EXAMPLE_URL);
        $doc->setMarkup(self::EXAMPLE_MARKUP);
        $doc->parse();

        $this->assertInstanceOf('DOMXPath', $doc->getXPath());
    }

    public function testIsVerified()
    {
        $doc = $this->createDocument(self::EXAMPLE_URL);
        $doc->setMarkup(self::EXAMPLE_MARKUP);
        $doc->parse();

        $this->assertTrue($doc->isVerified());
    }

    public function testIsNotVerified()
    {
        $parser = $this->createParserMock();
        $parser->addVerifyRule(new XPath("//a[contains(./text(), 'cad')]"), false);

        $doc = new Document(self::EXAMPLE_URL, $parser);
        $doc->setMarkup(self::TWO_LINKS_MARKUP);
        $doc->parse();

        $this->assertFalse($doc->isVerified());
    }

    public function testIsIndexable()
    {
        $doc = $this->createDocument(self::EXAMPLE_URL);
        $doc->setMarkup(self::EXAMPLE_MARKUP);
        $doc->parse();

        $this->assertTrue($doc->isIndexable());
    }

    public function testGetLinks()
    {
        $doc = $this->createDocument(self::EXAMPLE_URL);
        $doc->setMarkup(self::EXAMPLE_MARKUP);
        $doc->parse();

        $this->assertCount(1, $doc->getLinks()->all());
    }

    public function testGetValuesStorage()
    {
        $doc = $this->createDocument(self::EXAMPLE_URL);
        $doc->setMarkup(self::EXAMPLE_MARKUP);
        $doc->parse();

        $this->assertCount(1, $doc->getValues()->get('pre'));
    }

    public function testParseAndIsParsed()
    {
        $doc = $this->createDocument(self::EXAMPLE_URL);
        $doc->setMarkup(self::EXAMPLE_MARKUP);

        $this->assertNull($doc->isParsed());

        $doc->parse();
        $this->assertTrue($doc->isParsed());
    }

    public function testCallOnParseCallback()
    {
        $doc = $this->createDocument(self::EXAMPLE_URL);
        $doc->setMarkup(self::EXAMPLE_MARKUP);

        $parser = $doc->getParser();
        $called = false;
        $parser->setOnParseCallback(function() use (&$called) {
            $called = true;
        });

        $doc->parse();
        $this->assertTrue($called);
    }
}
<?php

namespace yCrawler\Parser\Item\Types;

use yCrawler\Parser\Exceptions;
use yCrawler\Document;

class RegExpType implements Type
{
    public function evaluate(Document $document, $pattern)
    {
        $result = $this->evaluateRegExp($document, $pattern);

        $output = [];
        foreach (end($result) as $index => $value) {
            $output[] = [
                'value' =>  $value,
                'full' =>  $result[0][$index],
                'node' => null,
                'dom' => $document->getDOM()
            ];
        }

        return $output;
    }

    private function evaluateRegExp(Document $document, $pattern)
    {
        $html = $document->getHTML();

        if (!preg_match_all($pattern, $html, $matches)) {
            throw new Exceptions\MalformedExpression(sprintf(
                'Malformed RegExp expression %s', $pattern
            ));
        }

        return $matches;
    }
}

<?php

namespace App\Twig\base;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;
use App\Service\base\StringHelper;
use Symfony\Component\DomCrawler\Crawler;
use Twig\TwigFilter;

class HtmlExtension extends AbstractExtension
{
    public function getFilters(): array
    {
        return [
            new TwigFilter('TBtxtfromhtml', [$this, 'txtfromhtml']),
        ];
    }
    public function getFunctions(): array

    {
        return [
            new TwigFunction('TBchangeTagName', [$this, 'changeTagName', ['is_safe' => ['html']]]),

        ];
    }
    public function txtfromhtml($str)
    {
        return str_replace('"', " ", strip_tags(html_entity_decode($str, ENT_QUOTES)));
    }
    public function changeTagName($node, $name)
    {
        $childnodes = [];
        foreach ($node->childNodes as $child) {
            $childnodes[] = $child;
        }
        $newnode = $node->ownerDocument->createElement($name);
        foreach ($childnodes as $child) {
            $child2 = $node->ownerDocument->importNode($child, true);
            $newnode->appendChild($child2);
        }
        if ($node->hasAttributes()) {
            foreach ($node->attributes as $attr) {
                $attrName = $attr->nodeName;
                $attrValue = $attr->nodeValue;
                $newnode->setAttribute($attrName, $attrValue);
            }
        }
        $node->parentNode->replaceChild($newnode, $node);
        return $newnode;
    }
}

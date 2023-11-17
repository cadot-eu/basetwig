<?php

namespace App\Twig\base;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;
use App\Service\base\StringHelper;
use Symfony\Component\DomCrawler\Crawler;
use Twig\TwigFilter;

class StringExtension extends AbstractExtension
{
    public function getFunctions(): array
    {
        return [
            new TwigFunction('TBkeywords', [$this, 'keywords', ['is_safe' => ['html'],],]),
            new TwigFunction('TBglossaire', [$this, 'glossaire', ['is_safe' => ['html'],],]),
        ];
    }
    public function getFilters(): array
    {
        return [
            new TwigFilter('TBtoString', [$this, 'tostring', ['is_safe' => ['html'],],]),
        ];
    }

    public function tostring($object)
    {
        switch (gettype($object)) {
            case 'string':
                return $object;
            case 'object':
                switch ($variable = get_class($object)) {
                    case 'DateTime':
                        return $object->format('d/m/Y');
                }
                return $object->toString();
            default:
                return $object;
        }
    }


    public function keywords($string, $number = 10)
    {
        return implode(',', StringHelper::keywords($string, $number));
    }
    public function glossaire($html, $glossaire)
    {
        $crawler = new Crawler($html);
        $domDocument = $crawler->getNode(0)->parentNode;
        foreach ($crawler->filter('body *') as $domElement) {
            if (isset($domElement->nodeValue)) {
                $texte = $domElement->nodeValue;
                foreach ($glossaire as $mot) {
                    $fmot = trim($mot->getTerme());
                }
            }
        }
        return $crawler->html();
    }
}

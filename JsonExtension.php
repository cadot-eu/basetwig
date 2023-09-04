<?php

namespace App\Twig\base;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;
use App\Service\base\StringHelper;
use Symfony\Component\DomCrawler\Crawler;
use Twig\TwigFilter;

class JsonExtension extends AbstractExtension
{
    public function getFilters(): array
    {
        return [
            new TwigFilter('TBjsonpretty', [
                $this,
                'jsonpretty',
                [
                    'is_safe' => ['html'],
                ],
            ]),
        ];
    }
    public function getFunctions(): array
    {
        return [
            new TwigFunction('TBjsondecode', [
                $this,
                'jsondecode',
                [
                    'is_safe' => ['html'],
                ],
            ]),

        ];
    }
    public function jsondecode($str, $arr = false)
    {
        return json_decode($str, $arr);
    }
    public function jsonpretty($json)
    {
        return json_decode($json);
        foreach (json_decode($json) as $key => $value) {
            $td = [];
            foreach ($value as $k => $v) {
                $td[] = "<b>$k</b>: $v";
            }
            $tr[] = \implode(',', $td);
        }

        return implode('<br>', $tr);
    }
}

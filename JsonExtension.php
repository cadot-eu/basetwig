<?php
namespace CadotEu\Crud\Twig\Extension;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

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

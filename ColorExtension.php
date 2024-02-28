<?php

namespace App\Twig\base;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;
use Faker\Factory;

class ColorExtension extends AbstractExtension
{
    public function getFunctions(): array
    {
        return [
            new TwigFunction('TBinttocolor', [
                $this,
                'inttocolor',
                [
                    'is_safe' => ['html'],
                ]

            ])
        ];
    }
    public static function inttocolor($int)
    {
        $red = ($int * 997) % 256;
        $green = ($int * 911) % 256;
        $blue = ($int * 751) % 256;

        $hex = sprintf("#%02x%02x%02x", $red, $green, $blue);
        return $hex;
    }
}

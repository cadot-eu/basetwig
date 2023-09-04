<?php

namespace App\Twig\base;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;
use Faker\Factory;

class FakerExtension extends AbstractExtension
{
    public function getFunctions(): array
    {
        return [
            new TwigFunction('TBfaker', [$this, 'faker', ['is_safe' => ['html']]]),
            new TwigFunction('TBfakeren', [$this, 'fakeren', ['is_safe' => ['html']]]),
            new TwigFunction('TBfakericon', [$this, 'fakericon', ['is_safe' => ['html']]]),
        ];
    }
    public static function faker($type = 'text', $options = null)
    {
        $faker = Factory::create('fr_FR');
        if ($options) {
            return $faker->$type($options);
        } else {
            return $faker->$type();
        }
    }

    public static function fakeren($type = 'text', $options = null)
    {
        $faker = Factory::create('fr_FR');
        if ($options) {
            return $faker->$type($options);
        } else {
            return $faker->$type();
        }
    }

    public static function fakericon($complet = true)
    {
        $list = json_decode(
            file_get_contents(__DIR__ . '../gists/list.json'),
            true
        );
        if ($complet == false) {
            return $list[array_rand($list)];
        } else {
            return 'bi bi-' . $list[array_rand($list)];
        }
    }
}

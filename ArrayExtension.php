<?php

namespace App\Twig\base;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;
use Faker\Factory;

class ArrayExtension extends AbstractExtension
{
    public function getFunctions(): array
    {
        return [
            new TwigFunction('array_position', [$this, 'findPositionInArray'])
        ];
    }
    public function findPositionInArray(array $array, $searchElement)
    {
        $position = array_search($searchElement, $array);

        // If element is not found, return false
        return ($position !== false) ? $position + 1 : false;
    }
}

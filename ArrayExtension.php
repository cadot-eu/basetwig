<?php
namespace CadotEu\Crud\Twig\Extension;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class ArrayExtension extends AbstractExtension
{
    public function getFunctions(): array
    {
        return [
            new TwigFunction('array_position', [$this, 'findPositionInArray']),
        ];
    }
    public function findPositionInArray(array $array, $searchElement)
    {
        $position = array_search($searchElement, $array);

        // If element is not found, return false
        return ($position !== false) ? $position + 1 : false;
    }
}

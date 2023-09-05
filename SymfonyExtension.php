<?php

namespace App\Twig\base;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;
use Faker\Factory;
use Twig\TwigFilter;

class SymfonyExtension extends AbstractExtension
{
    public function getFilters(): array
    {
        return [
            new TwigFilter('TBobjetProperties', [$this, 'objetProperties']),
            new TwigFilter('TBclass', [$this, 'class']),
            new TwigFilter('TBclassNom', [$this, 'classNom']),
        ];
    }
    public function getFunctions(): array
    {
        return [
            new TwigFunction('TBreorder', [$this, 'shema', ['is_safe' => ['html']]]),
        ];
    }
    public function reorder($repository, $donnees = '')
    {
        return $this->reorder($repository, $donnees);
    }
    public function objetProperties($objets)
    {
        $response = [];
        if (is_array($objets)) {
            $objets = $objets[0];
        }
        foreach ((array) $objets as $key => $value) {
            $string = preg_replace('/[\x00]/u', '\\', $key);
            $clef = substr($string, strrpos($string, '\\') + 1);
            $response[] = $clef;
        }
        return $response;
    }
    public function class($objet)
    {
        return get_class($objet);
    }
    public function classNom($objet)
    {
        return strtolower(get_class($objet));
    }
}

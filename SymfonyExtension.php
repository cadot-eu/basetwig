<?php
namespace CadotEu\Crud\Twig\Extension;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

class SymfonyExtension extends AbstractExtension
{

    /**
     * Retourne la liste des filtres Twig de l'extension :
     * - TBobjetProperties : extrait les noms de propriétés d'un objet ou d'un tableau d'objets.
     * - TBclass : retourne le nom de la classe d'un objet.
     */
    public function getFilters(): array
    {
        return [
            new TwigFilter('TBobjetProperties', [$this, 'objetProperties']),
            new TwigFilter('TBclass', [$this, 'class']),
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
            $string     = preg_replace('/[\x00]/u', '\\', $key);
            $clef       = substr($string, strrpos($string, '\\') + 1);
            $response[] = $clef;
        }
        return $response;
    }
    public function class ($objet)
    {
        return get_class($objet);
    }
}

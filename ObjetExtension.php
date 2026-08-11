<?php
namespace CadotEu\Crud\Twig\Extension;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

/**
 * Extension Twig pour les objets.
 *
 * Fonctions disponibles :
 * - groupBy : Regroupe les éléments par une propriété imbriquée.
 */
class ObjetExtension extends AbstractExtension
{

    public function getFilters(): array
    {
        return [
            new TwigFilter('TBgroupBy', [$this, 'groupBy']),
        ];
    }
    public function getFunctions(): array
    {
        return [
            new TwigFunction('TBgetProps', [$this, 'getProps']),
        ];
    }

    /**
     * Regroupe les éléments par une propriété imbriquée.
     *
     * @param array|object[] $elements Les éléments à regrouper.
     * @param string         $by       La propriété de regroupement au format "propriete.clef".
     *
     * @return array{result: array, group: array}
     */
    public function groupBy($elements, $by)
    {
        $explode = explode('.', $by);
        $prop    = $explode[0];
        $clef    = $explode[1];
        $result  = [];
        $props   = [];
        $group   = [];
        foreach ($elements as $element) {
            $get  = 'get' . ucfirst($prop);
            $sget = 'get' . ucfirst($clef);
            foreach ($element->$get() as $item) {
                $group[$item->$sget()] = $item;
                if (! isset($result[$item->$sget()])) {
                    $result[$item->$sget()] = [$element];
                } else {
                    $result[$item->$sget()][] = $element;
                }

            }
        }
        return ['result' => $result, 'group' => $group];
    }
    public function getProps($entity)
    {
        $reflection = new \ReflectionClass($entity);
        $props      = [];
        foreach ($reflection->getProperties() as $property) {
            $property->setAccessible(true);
            if ($property->isInitialized($entity)) {
                $props[] = $property->getName();
            }
        }
        return $props;
    }
}

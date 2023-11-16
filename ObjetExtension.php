<?php

namespace App\Twig\base;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;
use Faker\Factory;
use Twig\TwigFilter;

class ObjetExtension extends AbstractExtension
{

    public function getFilters(): array
    {
        return [
            new TwigFilter('TBgroupBy', [$this, 'groupBy']),
        ];
    }
    public function groupBy($elements, $by)
    {
        $explode = explode('.', $by);
        $prop = $explode[0];
        $clef = $explode[1];
        $result = [];
        $props = [];
        $group = [];
        foreach ($elements as $element) {
            $get = 'get' . ucfirst($prop);
            $sget = 'get' . ucfirst($clef);
            foreach ($element->$get() as $item) {
                $group[$item->$sget()] = $item;
                if (!isset($result[$item->$sget()])) {
                    $result[$item->$sget()] = [$element];
                } else
                    $result[$item->$sget()][] = $element;
            }
        }
        return ['result' => $result, 'group' => $group];
    }
}

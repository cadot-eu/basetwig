<?php

namespace App\Twig\base;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;
use Twig\TwigFilter;

class DatetimeExtension extends AbstractExtension
{
    public function getFunctions(): array
    {
        return [
            new TwigFunction('TBdatefr', [$this, 'datefr'])
        ];
    }
    public function getFilters()
    {
        return [
            new TwigFilter('TBisDate', [$this, 'isdate'])
        ];
    }

    public function datefr($date, $format)
    {
        $english_days = [
            'Monday',
            'Tuesday',
            'Wednesday',
            'Thursday',
            'Friday',
            'Saturday',
            'Sunday',
        ];
        $french_days = [
            'lundi',
            'mardi',
            'mercredi',
            'jeudi',
            'vendredi',
            'samedi',
            'dimanche',
        ];
        $english_months = [
            'January',
            'February',
            'March',
            'April',
            'May',
            'June',
            'July',
            'August',
            'September',
            'October',
            'November',
            'December',
        ];
        $french_months = [
            'janvier',
            'février',
            'mars',
            'avril',
            'mai',
            'juin',
            'juillet',
            'août',
            'septembre',
            'octobre',
            'novembre',
            'décembre',
        ];
        return str_replace(
            $english_months,
            $french_months,
            str_replace(
                $english_days,
                $french_days,
                date($format, strtotime($date))
            )
        );
    }
    public function isdate($date)
    {
        if (is_a($date, 'DateTime')) {
            return true;
        }
        //test si $date est une date
        try {
            $date = new \DateTime($date);

            return true;
        } catch (\Exception $e) {
            return false;
        }
    }
}

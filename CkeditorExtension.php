<?php

namespace App\Twig\base;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;
use Faker\Factory;
use Symfony\Component\DomCrawler\Crawler;
use Twig\TwigFilter;

class CkeditorExtension extends AbstractExtension
{
    public function getFilters(): array
    {
        return [
            new TwigFilter('TBckclean', [$this, 'ckclean', ['is_safe' => ['html'],],]),
        ];
    }
    public function getFunctions(): array
    {
        return [
            new TwigFunction('TBckintro', [$this, 'ckintro', ['is_safe' => ['html'],],]),
            new TwigFunction('TBcktexte', [$this, 'cktexte', ['is_safe' => ['html'],],]),
            new TwigFunction('TBcktags', [$this, 'cktags', ['is_safe' => ['html'],],]),

        ];
    }

    public function cktags($string, $tag)
    {
        $tab = [];
        $crawler = new Crawler($string);
        foreach ($crawler->filter($tag) as $item) {
            $tab[] = $item->nodeValue;
        }
        return $tab;
    }


    /**
     * If the string is longer than the limit, break it at the last space before the limit and add the pad
     *
     * @param string The string to be trimmed.
     * @param limit The maximum number of characters to return.
     * @param break The character you want to break the string at.
     * @param pad The string to append to the end of the truncated string.
     *
     * @return The first 500 characters of the string, or the first 500 characters before the first <div
     * class="page-break"> tag.
     */
    static function ckintro($string = '', $limit = 500, $break = ' ', $pad = '...'): string
    {
        if ($string == '' || $string == null) {
            return '';
        }
        //si on a la présence de la balise page-break
        if (strpos($string, '<div class="page-break"') !== false) {
            return html_entity_decode(
                strip_tags(explode('<div class="page-break"', $string)[0])
            );
        }
        //si on a la présence de la balise __se__format__replace_page_break
        elseif (strpos($string, '__se__format__replace_page_break') !== false) {
            return html_entity_decode(
                strip_tags(explode('__se__format__replace_page_break', $string)[0])
            );
        }
        //si on est dans un template on retourne le premier blockquote
        elseif (strpos($string, 'øtitreø') !== false) {
            $crawler = new Crawler($string);
            return $crawler->filter('blockquote')->getNode(0)->textContent;
        } else {
            // return with no change if string is shorter than $limit
            if (strlen($string) <= $limit) {
                return $string;
            }

            if (!is_array($break)) {
                $string = substr($string, 0, $limit);
                if (false !== ($breakpoint = strrpos($string, $break))) {
                    $string = substr($string, 0, $breakpoint);
                }
                $string = StringExtension::intro($string);
            }

            return strip_tags(html_entity_decode(strip_tags($string) . $pad));
        }
    }
    /**
     * If the string is longer than the limit, break it at the last space before the limit and add the pad
     *
     * @param string The string to be trimmed.
     * @param limit The maximum number of characters to return.
     * @param break The character you want to break the string at.
     * @param pad The string to append to the end of the truncated string.
     *
     * @return The first 500 characters of the string, or the first 500 characters before the first <div
     * class="page-break"> tag.
     */
    public static function cktexte($string)
    {
        $crawler = new Crawler($string);

        if ($crawler->filter('div.__se__format__replace_page_break')->count() > 0) {
            //on renvoie les noeuds qui sont parès ce noeud
            return $crawler->filter('div.__se__format__replace_page_break')->getNode(0)->nextSibling->textContent;
        }
    }
    public static function ckclean($string)
    {
        return preg_replace(
            '/<\\/?p(\\s+.*?>|>)/',
            '',
            html_entity_decode($string)
        );
    }
}

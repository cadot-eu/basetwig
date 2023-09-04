<?php

namespace App\Twig\base;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;
use Faker\Factory;
use Twig\TwigFilter;
use Symfony\Component\DomCrawler\Crawler;

class OldExtension extends AbstractExtension
{
    public function getFilters()
    {
        return [
            new TwigFilter('TBaddclass', [$this, 'addclass', ['is_safe' => ['html']],]),
            new TwigFilter('TBonlybalise', [$this, 'onlybalise', ['is_safe' => ['html']],])
        ];
    }
    public function getFunctions(): array
    {
        return [
            new TwigFunction('TBImage', [$this, 'img', ['is_safe' => ['html']],]),
            new TwigFunction('TBjsrender', [$this, 'ejsrender', ['is_safe' => ['html']],]),
            new TwigFunction('TBjsfirstImage', [$this, 'ejsfirstImage', ['is_safe' => ['html']],]),
            new TwigFunction('TBjsfirstHeader', [$this, 'ejsfirstHeader', ['is_safe' => ['html']],]),
            new TwigFunction('TBjsfirstText', [$this, 'ejsfirstText', ['is_safe' => ['html']],])
        ];
    }
    public function ejsrender($json, $quality = 'fullhd')
    {
        $container = new ContainerInterface();
        //dump($json);
        $tabs = json_decode($json);
        //on liste les objets
        foreach ($tabs->blocks as $num => $tab) {
            $data = '';
            switch ($tab->type) {
                case 'paragraph':
                case 'header':
                    $data = $tab->data->text;
                    if (substr(html_entity_decode($data), 0, 2) == '¤') {
                        $tabs->blocks[$num]->data->text = substr($tab->data->text, 2);
                    }
                    break;
                case 'image':
                    $data = $tab->data->caption;
                    if (substr(html_entity_decode($data), 0, 2) == '¤') {
                        $tabs->blocks[$num]->data->caption = substr(
                            $tab->data->caption,
                            2
                        );
                    }
                    $width = getimagesize(getcwd() . $tab->data->url)[0];
                    //limit width
                    if ($width > 1920) {
                        $imagineCacheManager = $this->container->get(
                            'liip_imagine.cache.manager'
                        );
                        $resolvedPath = $imagineCacheManager->getBrowserPath(
                            $tab->data->url,
                            $quality
                        );
                        $tabs->blocks[$num]->data->url = $resolvedPath;
                    }
                    break;
            }
            //si pas le droit de voir on supprime
            if (strpos($data, '¤') !== false) {
                if (
                    substr(html_entity_decode($data), 0, 2) == '¤' and
                    $this->roles == null
                ) {
                    unset($tabs->blocks[$num]);
                }
            }
        }

        $json = json_encode($tabs);
        $html = null;
        if ($tabs->blocks) {
            $html = new \Twig\Markup(Parser::parse($json)->toHtml(), 'UTF-8');
        }
        //ajout des finctionnalitées propre à mickcrud
        //travaille sur les images en ajoutant un filtre liip
        return $html;
    }

    public function ejsfirstImage($json)
    {
        $tab = json_decode($json)->blocks;
        foreach ($tab as $key => $value) {
            if ($value->type == 'image') {
                if (
                    $this->roles != null or
                    substr(html_entity_decode($value->data->caption), 0, 2) != '¤'
                ) {
                    return $value->data->url;
                }
            }
        }
        //return $html;
    }

    public function ejsfirstHeader($json)
    {
        $tab = json_decode($json)->blocks;
        foreach ($tab as $key => $value) {
            if ($value->type == 'header') {
                if (
                    $this->roles != null or
                    substr(html_entity_decode($value->data->text), 0, 2) != '¤'
                ) {
                    return strip_tags(str_replace('¤', '', $value->data->text));
                }
            }
        }
        //return $html;
    }

    public function ejsfirstText($json)
    {
        $tab = json_decode($json)->blocks;
        foreach ($tab as $key => $value) {
            if ($value->type == 'paragraph') {
                if (
                    $this->roles != null or
                    substr(html_entity_decode($value->data->text), 0, 2) != '¤'
                ) {
                    return strip_tags(str_replace('¤', '', $value->data->text));
                }
            }
        }
    }

    public static function onlybalise($string, $balise)
    {
        //on extrait la balise
        $crawler = new Crawler($string);
        if ($crawler->filter($balise)->count() > 0)
            return $crawler->filter($balise)->html();
        else return '';
    }
    public static function addclass($string, $class)
    {
        //dans le string on cheche class="" et on ajoute $class
        return preg_replace(
            '/class="([^"]*)"/',
            'class="$1 ' . $class . '"',
            $string
        );
    }
    public function img(
        $image,
        $size = '',
        $class = '',
        $style = '',
        $tooltip = ''
    ) {
        $taille = '100%';
        if (substr($size, 0, strlen('col')) == 'col') {
            $taille = strval(intval((intval(substr($size, 3)) * 100) / 12)) . 'vw';
        }
        if (substr($size, -2) == 'vw') {
            $taille = $size;
        }
        if (substr($size, -1) == '%') {
            $taille = $size;
        }
        $tab = explode('/', $image);
        $alt = str_replace('_', ' ', explode('.', end($tab))[0]);
        $alt = str_replace('-', "'", $alt);
        $return =
            '
             <img src="' .
            $this->CacheManager->getBrowserPath(
                $this->Package->getUrl($image),
                'lazy'
            ) .
            '" 
             data-srcset="
               ' .
            $this->CacheManager->getBrowserPath(
                $this->Package->getUrl($image),
                'mini'
            ) .
            ' 100w,
              ' .
            $this->CacheManager->getBrowserPath(
                $this->Package->getUrl($image),
                'petit'
            ) .
            ' 300w,
               ' .
            $this->CacheManager->getBrowserPath(
                $this->Package->getUrl($image),
                'semi'
            ) .
            ' 450w,
             ' .
            $this->CacheManager->getBrowserPath(
                $this->Package->getUrl($image),
                'moyen'
            ) .
            ' 600w,
             ' .
            $this->CacheManager->getBrowserPath(
                $this->Package->getUrl($image),
                'grand'
            ) .
            ' 900w"
             class="lazyload ' .
            $class .
            '" data-sizes="auto"
            style="width:' .
            $taille .
            ';' .
            $style .
            '" alt="' .
            ucfirst($alt) .
            '"';
        $return .=
            'data-toggle="tooltip" data-placement="top" title="' . $tooltip . '"';
        return $return . ' />';
    }
}

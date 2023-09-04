<?php

namespace App\Twig\base;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;
use Faker\Factory;

class SeoExtension extends AbstractExtension
{
    public function getFunctions(): array
    {
        return [
            new TwigFunction('TBshema', [
                $this,
                'shema',
                [
                    'is_safe' => ['html'],
                ]

            ])
        ];
    }
    public function shema($type, $json)
    {
        $res['@context'] = 'http://schema.org';
        $res['@type'] = ucfirst($json['@type']);
        switch (strtolower($type)) {
            case 'article':
                $find = [
                    'name',
                    'datePublished',
                    'dateModified',
                    'articleSection',
                    'articleBody',
                    'url',
                    'headline',
                    'dateModified',
                    'datePublished',
                    'dateCreated',
                    'keywords',
                    'thumbnailUrl',
                    'image',
                    'headline',
                    'author',
                ];
                foreach ($json as $j => $val) {
                    if (in_array($j, $find)) {
                        $res[$j] = $val;
                    }
                }
                break;

            default:
                dd("ce type de shema n'est pas reconnu");
                break;
        }
        return '<script type="application/ld+json">' .
            "\n" .
            json_encode($res, JSON_UNESCAPED_SLASHES) .
            '</script>';
    }
}

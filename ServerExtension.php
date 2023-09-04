<?php

namespace App\Twig\base;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;
use Jaybizzle\CrawlerDetect\CrawlerDetect;

class ServerExtension extends AbstractExtension
{
    public function getFunctions(): array
    {
        return [
            new TwigFunction('TBbot', [
                $this,
                'bot',
                [
                    'is_safe' => ['html'],
                ],
            ]),
            new TwigFunction('TBuploadmax', [
                $this,
                'max',
                [
                    'is_safe' => ['html'],
                ],
            ]),
        ];
    }
    public function max()
    {
        $max_upload = (int) ini_get('upload_max_filesize');
        $max_post = (int) ini_get('post_max_size');
        $memory_limit = (int) ini_get('memory_limit');
        return min($max_upload, $max_post, $memory_limit);
    }
    public function bot($userAgent)
    {
        $CrawlerDetect = new CrawlerDetect();
        if ($CrawlerDetect->isCrawler($userAgent)) {
            return $CrawlerDetect->getMatches();
        }
    }
}

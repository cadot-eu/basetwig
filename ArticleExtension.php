<?php

namespace App\Twig\base;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;
use Faker\Factory;
use App\Service\base\ArticleHelper;
use Twig\TwigFilter;

class ArticleExtension extends AbstractExtension
{
    public function getFilters(): array
    {
        return [
            new TwigFilter('TBArticleSommaire', [$this, 'articlesommaire']),
            new TwigFilter('TBArticleVideo', [$this, 'articlevideo']),
            new TwigFilter('TBArticleAll', [$this, 'articleall']),


        ];
    }
    public function articlesommaire($str)
    {
        return ArticleHelper::getSommaire($str);
    }
    public function articlevideo($str)
    {
        return ArticleHelper::addLinkVideos($str);
    }
    public function articleall($str)
    {
        return ArticleHelper::getSommaire(ArticleHelper::addLinkVideos($str));
    }
}

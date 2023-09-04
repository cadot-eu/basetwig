<?php

namespace App\Twig\base;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;
use App\Service\base\FileUploader;

class FileExtension extends AbstractExtension
{
    public function getFunctions(): array
    {
        return [
            new TwigFunction('TBgetPublic', [$this, 'TBgetPublic']),
            new TwigFunction('TBgetFilename', [$this, 'TBgetFilename'])
        ];
    }
    public function TBgetFilename(string $file): string
    {
        return FileUploader::cleanname($file);
    }

    public function TBgetPublic($string): string
    {
        //si on a public
        $string = str_replace(['public/', '//'], ['', '/'], $string);
        //si on est en relatif
        if (substr($string, 0, 1) != '/') {
            $string = '/' . $string;
        }
        return $string;
    }
}

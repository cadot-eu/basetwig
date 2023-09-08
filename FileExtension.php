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
            new TwigFunction('TBgetFilename', [$this, 'TBgetFilename']),
            new TwigFunction('TBremoveHost', [$this, 'removeHost']),
        ];
    }
    public function removeHost($url): string
    {
        // Utilisez parse_url pour extraire les composants de l'URL
        $parsedUrl = parse_url($url);

        // Vérifiez si le composant "host" existe (le domaine)
        if (isset($parsedUrl['host'])) {
            // Supprimez le domaine du début de l'URL
            $urlSansDomaine = $parsedUrl['path'];
            // Ajoutez un slash initial si nécessaire
            if (strpos($urlSansDomaine, '/') !== 0) {
                $urlSansDomaine = '/' . $urlSansDomaine;
            }

            // $urlSansDomaine contiendra l'URL sans le domaine
            return $urlSansDomaine; // Affiche "/path/to/resource"
        } else {
            return false;
        }
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

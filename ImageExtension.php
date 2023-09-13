<?php

namespace App\Twig\base;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;
use ImagickPixel;
use Imagick;

class ImageExtension extends AbstractExtension
{
    public function getFunctions(): array
    {
        return [
            new TwigFunction('TBimgToBase64', [$this, 'TBimgToBase64', ['is_safe' => ['html'],],]),
            new TwigFunction('TBIconFromBootstrap', [$this, 'IconFromBootstrap', ['is_safe' => ['html'],],]),
        ];
    }
    public static function TBimgToBase64($urlOrFile, $inline = false)
    {
        if (!filter_var($urlOrFile, FILTER_VALIDATE_URL)) {
            if (file_exists('/app/public/' . $urlOrFile)) {
                $url = '/app/public/' . $urlOrFile;
                $content = file_get_contents($url);
            } else
                return false;
        } else {
            if (file_exists($urlOrFile)) {
                $url = $urlOrFile;
                $content = file_get_contents($urlOrFile);
            } else
                return false;
        }
        return $inline ? sprintf('data:image/%s;base64,%s', pathinfo($url, PATHINFO_EXTENSION), base64_encode($content)) : base64_encode($content);
    }
    public function getico($file, $taille = 32)
    {
        //pour prendre directement en public
        if (!file_exists($file)) {
            if (file_exists('/app/public' . $file)) {
                $file = '/app/public' . $file;
            }
            if (file_exists('/app/public/' . $file)) {
                $file = '/app/public/' . $file;
            }
            if (file_exists('/app/public/uploads/' . $file)) {
                $file = '/app/public/uploads/' . $file;
            }
        }

        $dom = new DOMDocument('1.0', 'utf-8');
        $adresse =
            '/app/vendor/wgenial/php-mimetypeicon/icons/scalable/' .
            str_replace('/', '-', mime_content_type($file)) .
            '.svg';
        $dom->load($adresse);
        $svg = $dom->documentElement;

        if (!$svg->hasAttribute('viewBox')) {
            // viewBox is needed to establish
            // userspace coordinates
            $pattern = '/^(\d*\.\d+|\d+)(px)?$/'; // positive number, px unit optional

            $interpretable =
                preg_match($pattern, $svg->getAttribute('width'), $width) &&
                preg_match($pattern, $svg->getAttribute('height'), $height);

            if ($interpretable) {
                $view_box = implode(' ', [0, 0, $width[0], $height[0]]);
                $svg->setAttribute('viewBox', $view_box);
            } else {
                // this gets sticky
                throw new Exception('viewBox is dependent on environment');
            }
        }

        $svg->setAttribute('width', $taille);
        $svg->setAttribute('height', $taille);
        $nom = uniqid();
        $dom->save('/tmp/' . $nom . '.svg');
        // if (!file_exists('/app/vendor/wgenial/php-mimetypeicon/icons/' . $taille)) {
        $adresse =
            '/app/vendor/wgenial/php-mimetypeicon/icons/scalable/' .
            str_replace('/', '-', mime_content_type($file)) .
            '.svg';
        return 'data:image/svg+xml;base64,' .
            base64_encode(
                $this->unescape(file_get_contents('/tmp/' . $nom . '.svg'))
            );
        // }

        //ancien système avec image
        // $adresse = '/app/vendor/wgenial/php-mimetypeicon/icons/' . $taille . '/' . str_replace('/', '-', mime_content_type(getcwd() . $file)) . '.png';
        // $imageData = base64_encode(file_get_contents($adresse));
        // return 'data: ' . mime_content_type(getcwd() . $file) . ';base64,' . $imageData;
    }
    //nettoie le svg pour pouvoir le convertir en base64
    private function unescape($str)
    {
        $ret = '';
        $len = strlen($str);
        for ($i = 0; $i < $len; $i++) {
            if ($str[$i] == '%' && $str[$i + 1] == 'u') {
                $val = hexdec(substr($str, $i + 2, 4));
                if ($val < 0x7f) {
                    $ret .= chr($val);
                } elseif ($val < 0x800) {
                    $ret .= chr(0xc0 | ($val >> 6)) . chr(0x80 | ($val & 0x3f));
                } else {
                    $ret .=
                        chr(0xe0 | ($val >> 12)) .
                        chr(0x80 | (($val >> 6) & 0x3f)) .
                        chr(0x80 | ($val & 0x3f));
                }
                $i += 5;
            } elseif ($str[$i] == '%') {
                $ret .= urldecode(substr($str, $i, 3));
                $i += 2;
            } else {
                $ret .= $str[$i];
            }
        }
        return $ret;
    }
    public function IconFromBootstrap($name, $color = '#000', $size = 32)
    {
        // Définir le chemin du fichier SVG correspondant au nom donné
        $svgPath = '/app/node_modules/bootstrap-icons/icons/' . $name . '.svg';

        //on modifie le fill
        try {
            // Lire le contenu du fichier SVG
            $svgContent = file_get_contents($svgPath);

            // Remplacer la couleur de remplissage actuelle par la couleur souhaitée
            $svgContent = str_replace('fill="currentColor"', 'fill="' . $color . '"', $svgContent);
            $svgContent = preg_replace('/width="[^"]+"/', 'width="1024"', $svgContent);
            $svgContent = preg_replace('/height="[^"]+"/', 'height="1024"', $svgContent);


            // Créer un objet Imagick à partir du fichier SVG
            $im = new Imagick();
            $im->setBackgroundColor(new ImagickPixel('transparent'));
            $im->readImageBlob($svgContent);
            $im->setImageFormat("png32");
            $pngBase64 = 'data:image/png;base64,' . base64_encode($im->getImageBlob());
            // Retourner le PNG encodé en base64
            return '<img src="' . $pngBase64 . '" width="' . $size . '" height="' . $size . '">';
        } catch (Exception $e) {
            // En cas d'erreur, vous pouvez choisir de renvoyer un message d'erreur ou une valeur par défaut
            return "Erreur : " . $e->getMessage();
        }
    }
}

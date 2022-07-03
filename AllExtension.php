<?php

namespace App\Twig\base;

use App\Service\base\HtmlHelper;
use App\Service\base\ToolsHelper;
use DOMDocument;
use Faker\Factory;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;
use App\Service\base\StringHelper;
use DOMElement;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use DOMNode;

use function PHPUnit\Framework\isEmpty;

class AllExtension extends AbstractExtension
{
    private $requestStack;

    public function __construct(RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
    }
    public function getFunctions(): array
    {
        return [

            /* --------------------- implementation de functions php -------------------- */
            new TwigFunction('TBdd', [$this, 'dd']),
            new TwigFunction('TBd', [$this, 'd']),
            new TwigFunction('TBgetenv', [$this, 'getenv']),
            /* -------------------------- functions d'affichage ------------------------- */
            new TwigFunction('TBdatefr', [$this, 'datefr']),
            new TwigFunction('TBuploadmax', [
                $this, 'max', [
                    'is_safe' => ['html'],

                ],
            ]),
            new TwigFunction('TBgetPublic', [$this, 'TBgetPublic']), // return a clean file for public access
            new TwigFunction('TBgetFilename', [$this, 'TBgetFilename']),
            new TwigFunction('TBimgToBase64', [
                $this, 'TBimgToBase64', [
                    'is_safe' => ['html'],

                ],
            ]),
            /* ----------------------------- other-fonctions ----------------------------- */
            new TwigFunction('TBjsondecode', [
                $this, 'jsondecode', [
                    'is_safe' => ['html'],

                ],
            ]),
            new TwigFunction('TBfaker', [
                $this, 'faker', [
                    'is_safe' => ['html'],

                ],
            ]),
            new TwigFunction('TBfakeren', [
                $this, 'fakeren', [
                    'is_safe' => ['html'],

                ],
            ]),
            new TwigFunction('TBfakericon', [
                $this, 'fakericon', [
                    'is_safe' => ['html'],

                ],
            ]),
            new TwigFunction('TBckintro', [
                $this, 'ckintro', [
                    'is_safe' => ['html'],

                ],
            ]),
            new TwigFunction('TBcktexte', [
                $this, 'cktexte', [
                    'is_safe' => ['html'],

                ],
            ]),
            new TwigFunction('TBshema', [
                $this, 'shema', [
                    'is_safe' => ['html'],

                ],
            ]),
            new TwigFunction('TBkeywords', [
                $this, 'keywords', [
                    'is_safe' => ['html'],

                ],
            ]),
            new TwigFunction('TBglossaire', [
                $this, 'glossaire', [
                    'is_safe' => ['html'],

                ],
            ]),
            new TwigFunction('TBcktags', [
                $this, 'cktags', [
                    'is_safe' => [
                        'html',
                    ],

                ],
            ]),



        ];
    }

    public function getFilters()
    {
        return [
            new TwigFilter('TBsanitize', [$this, 'sanitize']),
            new TwigFilter('TBobjetProperties', [$this, 'objetProperties']),
            new TwigFilter('TBtxtfromhtml', [$this, 'txtfromhtml']),
            new TwigFilter('TBlang', [$this, 'lang']),
            new TwigFilter('TBjsonpretty', [
                $this, 'jsonpretty', [
                    'is_safe' => ['html'],

                ],

            ]),
            /* -------------------------------- ckeditor -------------------------------- */
            new TwigFilter('TBckclean', [
                $this, 'ckclean', [
                    'is_safe' => [
                        'html',
                    ],

                ],
            ]),


        ];
    }


    /* -------------------------------------------------------------------------- */
    /*                       implementation de functions php                      */
    /* -------------------------------------------------------------------------- */
    public function reorder($repository, $donnees = '')
    {
        return $this->reorder($repository, $donnees);
    }

    public function dd($value)
    {
        dd($value);
    }
    public function d($value)
    {
        dump($value);
    }
    public function getenv(string $var)
    {
        return $_ENV[$var];
    }
    /* -------------------------------------------------------------------------- */
    /*                            functions d'affichage                           */
    /* -------------------------------------------------------------------------- */

    /**
     * TBgetFilename return a filename without directory and uniqid
     */
    public function TBgetFilename(string $file): string //example /app/public/uploads/fichier/toto-test-1232.doc.jpg
    {
        $info = pathinfo($file);
        if (strpos($info['filename'], '-')) {
            $filename = substr($info['filename'], 0, strrpos($info['filename'], '-'));
        } else {
            $filename = $info['filename'];
        }
        return $filename . '.' . $info['extension'];
    }

    /**
     * TBgetpublic return good url of a file public
     *
     * @param  mixed $string
     */
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

    public function max()
    {
        $max_upload = (int) (ini_get('upload_max_filesize'));
        $max_post = (int) (ini_get('post_max_size'));
        $memory_limit = (int) (ini_get('memory_limit'));
        return (min($max_upload, $max_post, $memory_limit));
    }

    public function sanitize($value)
    {
        return $this->sanitize($value);
    }

    public function objetProperties($objets)
    {
        $response = [];
        if (is_array($objets)) {
            $objets = $objets[0];
        }
        foreach ((array) $objets as $key => $value) {
            $string = preg_replace('/[\x00]/u', '\\', $key);
            $clef = substr($string, strrpos($string, '\\') + 1);
            $response[] = $clef;
        }
        return $response;
    }

    public function txtfromhtml($str)
    {
        return strip_tags(html_entity_decode($str, ENT_QUOTES));
    }

    /**
     * It takes a JSON string and returns a pretty-printed HTML table
     *
     * @param json The JSON string to be formatted.
     */
    public function jsonpretty($json)
    {
        return json_decode($json);
        foreach (json_decode($json) as $key => $value) {
            $td = [];
            foreach ($value as $k => $v) {
                $td[] = "<b>$k</b>: $v";
            }
            $tr[] = \implode(',', $td);
        }

        return implode('<br>', $tr);
    }
    /**
     * It takes a string of HTML, finds all the `<span>` tags with a `lang` attribute, and removes them
     * if the `lang` attribute doesn't match the current locale
     * 
     * @param html the html to be filtered
     * @param lang The name of the filter.
     * 
     * @return The HTML of the page with the span tags removed.
     */
    public function lang($html, $lang = ''): ?string
    {
        if (!$html) return null;
        $locale = $lang ?: $this->requestStack->getCurrentRequest()->getLocale();
        $crawler = new Crawler($html);
        foreach ($crawler->filter('span[lang!=' . $locale . ']') as $node) {
            $node->parentNode->removeChild($node);
        }
        foreach ($crawler->filter('*') as $node) {
            if (trim(html_entity_decode($node->nodeValue), " \t\n\r\0\x0B\xC2\xA0") == '')
                $node->parentNode->removeChild($node);
            // else dump($node->nodeValue);
        }



        return  HtmlHelper::remove_html_tags($crawler->outerHtml(), ['body', 'html']);
    }




    //convertie une date anglaise en fr
    //de la forme datetime
    public function datefr($date, $format)
    {
        $english_days = [
            'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday',
        ];
        $french_days = ['lundi', 'mardi', 'mercredi', 'jeudi', 'vendredi', 'samedi', 'dimanche'];
        $english_months = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
        $french_months = ['janvier', 'février', 'mars', 'avril', 'mai', 'juin', 'juillet', 'août', 'septembre', 'octobre', 'novembre', 'décembre'];
        return str_replace($english_months, $french_months, str_replace($english_days, $french_days, date($format, strtotime($date))));
    }

    // renvoie directement une balise img avec son src avec plusieurs taille en fonction de la largeur d'écran
    // combiné avec liipimagine, supporte les class, les styles et le lazy
    public function img($image, $size = '', $class = '', $style = '', $tooltip = '')
    {
        $taille = '100%';
        if (substr($size, 0, strlen('col')) == 'col') {
            $taille = strval(intval(intval(substr($size, 3)) * 100 / 12)) . 'vw';
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
        $return = '
             <img src="' . $this->CacheManager->getBrowserPath($this->Package->getUrl($image), "lazy") . '" 
             data-srcset="
               ' . $this->CacheManager->getBrowserPath($this->Package->getUrl($image), "mini") . ' 100w,
              ' . $this->CacheManager->getBrowserPath($this->Package->getUrl($image), "petit") . ' 300w,
               ' . $this->CacheManager->getBrowserPath($this->Package->getUrl($image), "semi") . ' 450w,
             ' . $this->CacheManager->getBrowserPath($this->Package->getUrl($image), "moyen") . ' 600w,
             ' . $this->CacheManager->getBrowserPath($this->Package->getUrl($image), "grand") . ' 900w"
             class="lazyload ' . $class . '" data-sizes="auto"
            style="width:' . $taille . ';' . $style . '" alt="' . ucfirst($alt) . '"';
        $return .= 'data-toggle="tooltip" data-placement="top" title="' . $tooltip . '"';
        return $return . ' />';
    }

    // renvoie une image en mini de 100px de large par défaut
    //modal permet de cliquer sur l'image pour avoir un apercu en grand
    // possibilité de donner des tailles par exemple:height:100px
    // on peux donner des classes et des styles
    public function thumbnail($image, $modal = true, $tooltip = '', $size = '', $class = '', $style = '')
    {
        $return = '';
        if ($size) {
            $taille = $size;
        } else {
            $taille = 'width:100px';
        }
        //détermination du alt
        $tab = explode('/', $image);
        $alt = str_replace('_', ' ', explode('.', end($tab))[0]);
        $alt = str_replace('-', "'", $alt);
        //si on a un tooltip
        if (!$tooltip) {
            $tooltip = $alt;
        } else {
            $alt = $tooltip;
        }
        //correction du répertoire
        if (isset($image)) {
            if (!file_exists($image)) {
                if (file_exists('/app/public/' . $image)) {
                    $image = '/' . $image;
                }
                if (file_exists('/app/public/uploads/' . $image)) {
                    $image = '/uploads/' . $image;
                }
            }
            $ext = strtolower(pathinfo($image, PATHINFO_EXTENSION));
            switch ($ext) {
                case 'jpg':
                case 'jpeg':
                case 'gif':
                case 'png':
                    $file = $image;
                    if ($modal !== false) {
                        $return =
                            "<a  data-toggle='popover-hover' style=\"cursor:zoom-in;\" data-img=\"" . $this->CacheManager->getBrowserPath($this->Package->getUrl($file), "grand") . "\">";
                    }
                    $return .= '
             <img title="' . $tooltip . '" src="' . $this->CacheManager->getBrowserPath($this->Package->getUrl($file), "mini") . '"
             class="' . $class . '" style="' . $taille . ';' . $style . '" alt="' . ucfirst($alt) . '"';

                    if ($modal !== false) {
                        $return .= "/></a>";
                    } else {
                        $return .= 'data-toggle="tooltip" data-placement="top" title="' . $tooltip . '" /></a>';
                    }
                    return $return;
                    break;
                default:
                    return "<img src='" . $this->getico($image) . "'>";
                    break;
            }
        } else {
            return 'image non trouvée';
        }
    }

    /**
     * getico return un html img avec une icone représentant l'extensio ndu fichier
     * si la taille est différente on met une taille à l'img
     *
     * @param   string  $file    fichier sur le disque
     * @param   int=32  $taille
     *
     * @return  string  img base64
     */
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
        $adresse = '/app/vendor/wgenial/php-mimetypeicon/icons/scalable/' . str_replace('/', '-', mime_content_type($file)) . '.svg';
        $dom->load($adresse);
        $svg = $dom->documentElement;

        if (!$svg->hasAttribute('viewBox')) { // viewBox is needed to establish
            // userspace coordinates
            $pattern = '/^(\d*\.\d+|\d+)(px)?$/'; // positive number, px unit optional

            $interpretable = preg_match($pattern, $svg->getAttribute('width'), $width) &&
                preg_match($pattern, $svg->getAttribute('height'), $height);

            if ($interpretable) {
                $view_box = implode(' ', [0, 0, $width[0], $height[0]]);
                $svg->setAttribute('viewBox', $view_box);
            } else { // this gets sticky
                throw new Exception("viewBox is dependent on environment");
            }
        }

        $svg->setAttribute('width', $taille);
        $svg->setAttribute('height', $taille);
        $nom = uniqid();
        $dom->save('/tmp/' . $nom . '.svg');
        // if (!file_exists('/app/vendor/wgenial/php-mimetypeicon/icons/' . $taille)) {
        $adresse = '/app/vendor/wgenial/php-mimetypeicon/icons/scalable/' . str_replace('/', '-', mime_content_type($file)) . '.svg';
        return "data:image/svg+xml;base64," . base64_encode($this->unescape(file_get_contents('/tmp/' . $nom . '.svg')));
        // }

        //ancien système avec image
        // $adresse = '/app/vendor/wgenial/php-mimetypeicon/icons/' . $taille . '/' . str_replace('/', '-', mime_content_type(getcwd() . $file)) . '.png';
        // $imageData = base64_encode(file_get_contents($adresse));
        // return 'data: ' . mime_content_type(getcwd() . $file) . ';base64,' . $imageData;
    }

    public function TBimgToBase64($url, $inline = false)
    {

        return $inline ? sprintf('data:image/%s;base64,%s', pathinfo($url, PATHINFO_EXTENSION), base64_encode($url)) : base64_encode($url);
    }
    /* -------------------------------------------------------------------------- */
    /*                            functions editeur ckeditor                      */
    /* -------------------------------------------------------------------------- */

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
    static  function ckintro($string, $limit = 500, $break = " ", $pad = "...")
    {
        if (strpos($string, '<div class="page-break"') !== false) {
            return html_entity_decode(explode('<div class="page-break"', $string)[0]);
        } else {
            // return with no change if string is shorter than $limit
            if (strlen($string) <= $limit) {
                return $string;
            }

            $string = substr($string, 0, $limit);
            if (false !== ($breakpoint = strrpos($string, $break))) {
                $string = substr($string, 0, $breakpoint);
            }

            return html_entity_decode(strip_tags($string) . $pad);
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
    static function cktexte($string)
    {
        if (strpos($string, '<div class="page-break"') !== false) {
            return html_entity_decode(explode('<div class="page-break"', $string)[1]);
        }

        return $string;
    }
    static function ckclean($string)
    {
        return preg_replace("/<\\/?p(\\s+.*?>|>)/", "", html_entity_decode($string));;
    }

    /* -------------------------------------------------------------------------- */
    /*                            functions editeur ejs                           */
    /* -------------------------------------------------------------------------- */
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
                        $tabs->blocks[$num]->data->caption = substr($tab->data->caption, 2);
                    }
                    $width = getimagesize(getcwd() . $tab->data->url)[0];
                    //limit width
                    if ($width > 1920) {
                        $imagineCacheManager = $this->container->get('liip_imagine.cache.manager');
                        $resolvedPath = $imagineCacheManager->getBrowserPath($tab->data->url, $quality);
                        $tabs->blocks[$num]->data->url = $resolvedPath;
                    }
                    break;
            }
            //si pas le droit de voir on supprime
            if (strpos($data, '¤') !== false) {
                if (substr(html_entity_decode($data), 0, 2) == '¤' and $this->roles == null) {
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
                if ($this->roles != null or substr(html_entity_decode($value->data->caption), 0, 2) != '¤') {
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
                if ($this->roles != null or substr(html_entity_decode($value->data->text), 0, 2) != '¤') {
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
                if ($this->roles != null or substr(html_entity_decode($value->data->text), 0, 2) != '¤') {
                    return strip_tags(str_replace('¤', '', $value->data->text));
                }
            }
        }
    }

    /* -------------------------------------------------------------------------- */
    /*                               sous-founctions                              */
    /* -------------------------------------------------------------------------- */
    //pour le svg
    //nettoie le svg pour pouvoir le convertir en base64
    public function unescape($str)
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
                    $ret .= chr(0xe0 | ($val >> 12)) . chr(0x80 | (($val >> 6) & 0x3f)) . chr(0x80 | ($val & 0x3f));
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

    /* -------------------------------------------------------------------------- */
    /*                               other functions                              */
    /* -------------------------------------------------------------------------- */
    public function jsondecode($str, $arr = false)
    {
        return json_decode($str, $arr);
    }

    static public function faker($type = 'text', $options = null)
    {
        $faker = Factory::create('fr_FR');
        if ($options) {
            return $faker->$type($options);
        } else {
            return $faker->$type();
        }
    }

    static public function fakeren($type = 'text', $options = null)
    {
        $faker = Factory::create('fr_FR');
        if ($options) {
            return $faker->$type($options);
        } else {
            return $faker->$type();
        }
    }

    /**
     * Returns a random icon bootstrap from the list of icons
     *
     * @param complet If set to false, the function will return a random icon from the list. If set to
     * true, it will return the complete icon name.
     *
     * @return An array of random icons.
     */
    static public function fakericon($complet = true)
    {
        $list = json_decode(file_get_contents(__DIR__ . '../gists/list.json'), true);
        if ($complet == false) {
            return $list[array_rand($list)];
        } else {
            return 'bi bi-' . $list[array_rand($list)];
        }
    }

    function innerHTML(\DOMNode $n, $include_target_tag = true)
    {
        $doc = new \DOMDocument();
        $doc->appendChild($doc->importNode($n, true));
        $html = trim($doc->saveHTML());
        if ($include_target_tag) {
            return $html;
        }
        return preg_replace('@^<' . $n->nodeName . '[^>]*>|</' . $n->nodeName . '>$@', '', $html);
    }
    function changeTagName($node, $name)
    {
        $childnodes = array();
        foreach ($node->childNodes as $child) {
            $childnodes[] = $child;
        }
        $newnode = $node->ownerDocument->createElement($name);
        foreach ($childnodes as $child) {
            $child2 = $node->ownerDocument->importNode($child, true);
            $newnode->appendChild($child2);
        }
        if ($node->hasAttributes()) {
            foreach ($node->attributes as $attr) {
                $attrName = $attr->nodeName;
                $attrValue = $attr->nodeValue;
                $newnode->setAttribute($attrName, $attrValue);
            }
        }
        $node->parentNode->replaceChild($newnode, $node);
        return $newnode;
    }
    /**
     * It takes a type and a json object as parameters, and returns a string containing the schema.org
     * markup
     * ajouter no_ pour ne pas afficher un élément
     * permet d'envoyer text pour extraire les keywords sans le retourner
     * 
     * @param type the type of schema you want to use.
     * @param json the json object to be converted to a schema.org object
     * 
     * @return the value of the variable .
     */

    function shema($type, $json)
    {
        $res['@context'] = "http://schema.org";
        $res['@type'] = ucfirst($json['@type']);
        switch (strtolower($type)) {
            case 'article':
                $find = ['name', 'datePublished', 'dateModified', 'articleSection', 'articleBody', 'url', 'headline', 'dateModified', 'datePublished', 'dateCreated', 'keywords', 'thumbnailUrl', 'image', 'headline', 'author'];
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
        return  '<script type="application/ld+json">' . "\n" . json_encode($res, JSON_UNESCAPED_SLASHES) . '</script>';
    }
    function keywords($string, $number = 10)
    {
        return implode(',', StringHelper::keywords($string, $number));
    }
    function glossaire($html, $glossaire)
    {
        $crawler = new Crawler($html);
        $domDocument = $crawler->getNode(0)->parentNode;
        foreach ($crawler->filter('body *') as $domElement) {
            if (isset($domElement->nodeValue)) {
                $texte = $domElement->nodeValue;
                foreach ($glossaire as $mot) {
                    $fmot = trim($mot->getTerme());
                }
            }
        }
        return $crawler->html();
    }
    private static function __appendHTML($parent, $rawHtml)
    {
        $tmpDoc = new DOMDocument();
        $tmpDoc->loadHTML($rawHtml);
        foreach ($tmpDoc->getElementsByTagName('body')->item(0)->childNodes as $node) {
            $importedNode = $parent->ownerDocument->importNode($node, TRUE);
            $parent->appendChild($importedNode);
        }
    }
    function cktags($string, $tag)
    {
        $tab = [];
        $crawler = new Crawler($string);
        foreach ($crawler->filter($tag) as $item)
            $tab[] = $item->nodeValue;
        return ($tab);
    }
}

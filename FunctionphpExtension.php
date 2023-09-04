<?php

namespace App\Twig\base;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;
use Twig\TwigFilter;

class FunctionphpExtension extends AbstractExtension
{
    public function getFilters(): array
    {
        return [
            new TwigFilter('TBisArray', [$this, 'isArray']),
            new TwigFilter('TBisObject', [$this, 'isObject']),
            new TwigFilter('TBsanitize', [$this, 'sanitize'])
        ];
    }
    public function getFunctions(): array
    {
        return [
            /* --------------------- implementation de functions php -------------------- */
            new TwigFunction('TBdd', [$this, 'dd']),
            new TwigFunction('TBd', [$this, 'd']),
            new TwigFunction('TBgetenv', [$this, 'getenv']),
            new TwigFunction('TBgetClass', [$this, 'getClass']),
            new TwigFunction('TBregex', [$this, 'regex']),
            new TwigFunction('TBpregReplace', [$this, 'pregReplace']),
        ];
    }
    public function dd($value)
    {
        dd($value);
    }
    public function d($value)
    {
        dump($value);
    }
    public function getenv(string $var): ?string
    {
        if (isset($_ENV[$var])) {
            return $_ENV[$var];
        } else {
            return null;
        }
    }
    public function getClass($object)
    {
        return (new \ReflectionClass($object))->getShortName();
    }
    public function regex($string, $regex)
    {
        preg_match_all($regex, $string, $matches);
        return $matches;
    }
    public function pregReplace($regex, $replace, $string)
    {
        return preg_replace($regex, $replace, $string);
    }
    public function isArray($var)
    {
        return is_array($var);
    }
    public function isObject($var)
    {
        return is_object($var);
    }
    public function sanitize($value)
    {
        return $this->sanitize($value);
    }
}

<?php
namespace RedirectTester;

class Helpers {

    public static function prepareUrl($url, $domain)
    {
        return "https://" . $domain . (substr($url, 0, 1) !== '/' ? '/' : '') . $url;
    }

    public static function validateUrl($url)
    {
        return filter_var($url, FILTER_VALIDATE_URL) !== false ? $url : '';
    }

    public static function adjustUrlEndingSlash($url, $shouldAdd)
    {
        $hasSlash = substr($url, -1) === '/';
        if ($shouldAdd && !$hasSlash) {
            return $url . '/';
        } elseif (!$shouldAdd && $hasSlash) {
            return substr($url, 0, -1);
        }
        return $url;
    }

    public static function parseRedirectLine($line,$domain)
    {
        $columns = explode(',', $line, 3);
        if (count($columns) < 2) {
            return null;
        }

        $columns = array_map(function ($value) use ($domain) {
            return self::prepareUrl(trim($value), $domain);
        }, array_slice($columns, 0, 2));


        $columns = array_map([ self::class ,'validateUrl'], $columns);

        return (!empty($columns[0]) && !empty($columns[1])) ? $columns : null;
    }


    public static function fetchEffectiveUrl($url)
    {
        $ch = curl_init(urldecode($url));
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.13) Gecko/20080311 Firefox/2.0.0.13');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_NOBODY, true);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 0);
        curl_setopt($ch, CURLOPT_VERBOSE, 0);
        curl_setopt($ch, CURLOPT_TIMEOUT, 60);
        curl_exec($ch);
        $redirectURL = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);
        curl_close($ch);
        return $redirectURL;
    }
}


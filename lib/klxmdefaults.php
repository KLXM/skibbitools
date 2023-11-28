<?php

class skibbiTools
{
    public static function mediaCopyright(string $file = '', string $type = 'text'): string
    {
        $output = '';
        if ($file != '') {
            if ($media = rex_media::get($file)) {
                $copyright = $media->getValue('med_copyright');
                $copyright_link = $media->getValue('med_copyright_link');
                if ($copyright_link != '' && $type == 'link') {
                    $output = '<a rel="noopener" href="'.$copyright_link.'">Copyright: '.$copyright.'</a>';
                } elseif ($copyright != '') {
                    $output = 'Copyright: '.$copyright;
                }
            }
        }
        return $output;
    }

    public static function truncateText(string $string, int $length = 300): string
    {
        $teaser = strip_tags($string);
        $teaserlen = mb_strlen($string);
        if ($teaserlen > $length) {
            $teaser = substr($teaser, 0, strpos($teaser, ".", $length) + 1);
        }
        return $teaser;
    }

    public static function formatGermanDate(string $date): string
    {
        $formatter = new \IntlDateFormatter('de_DE', \IntlDateFormatter::LONG, \IntlDateFormatter::NONE);
        return $formatter->format(strtotime($date));
    }

    public static function checkUrl(?string $url): ?string
    {
        if ($url) {
            if (filter_var($url, FILTER_VALIDATE_URL) === false) {
                return null;
            }
            if (file_exists(rex_path::media($url)) === true) {
                $url = rex_url::media($url);
            } else {
                if (filter_var($url, FILTER_VALIDATE_URL) === false && is_numeric($url)) {
                    $url = rex_getUrl($url);
                }
            }
            $link = $url;
            return $link;
        }
        return null;
    }
}

<?php

class skibbiTools
{
    public static function cke5LightboxHelper(): void
    {
        rex_extension::register('OUTPUT_FILTER', function (rex_extension_point $ep) {
            $html = $ep->getSubject();
            // Ersetze alle gefundene <figure> Tags mit der Callback-Funktion
            $html = preg_replace_callback('/<figure\b[^>]*\bclass\s*=\s*["\'][^"\']*?\bimage\b[^"\']*["\'][^>]*>.*?<a[^>]+href=[\'"]([^\'"]+?\.(jpg|jpeg|png|mp4|gif))[\'"][^>]*><img[^>]+src=[\'"]([^\'"]+?)[\'"][^>]*>.*?<\/figure>/i', function ($matches) {
                // Hole die abgeglichenen Werte
                $link = $matches[0];
                $href = $matches[1];
                $ext = $matches[2];
                $src = $matches[3];

                // Überprüfe, ob der href-Wert auf .jpg, .jpeg, .png oder .gif endet
                if (in_array($ext, ['jpg', 'jpeg', 'png', 'mp4', 'gif'])) {
                    // Ersetze das <figure> Tag mit der aktualisierten Version
                    return str_replace('<figure ', '<figure uk-lightbox ', $link);
                }
                // Ansonsten gib das Original zurück
                return $link;
            }, $html);
        }, rex_extension::LATE);
    }

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
        // END: abpxx6d04wxr

        // FILEPATH: Untitled-1
        // BEGIN: be15d9bcejpp
        return $teaser;
        // END: be15d9bcejpp
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

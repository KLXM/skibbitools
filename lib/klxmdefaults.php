<?php
class SkibbiTools
{
    public static function cke5LightboxHelper(): void
    {
        if (rex::isFrontend()) {{
            // Code hier einfügen

            rex_extension::register('OUTPUT_FILTER', function (rex_extension_point $ep) {
                $html = $ep->getSubject();

                // Verwende reguläre Ausdrücke, um verlinkte Bilder im HTML zu finden
                preg_match_all('/<figure\b[^>]*\bclass\s*=\s*["\'][^"\']*?\bimage\b[^"\']*["\'][^>]*>.*?<a[^>]+href=[\'"]([^\'"]+?\.(jpg|jpeg|png|mp4|gif))[\'"][^>]*><img[^>]+src=[\'"]([^\'"]+?)[\'"][^>]*>.*?<\/figure>/i', $html, $matches, PREG_SET_ORDER);

                // Durchlaufe alle Treffer
                foreach ($matches as $match) {
                    // Hole die abgeglichenen Werte
                    $link = $match[0];
                    $href = $match[1];
                    $ext = $match[2];
                    $src = $match[3];

                    // Überprüfe, ob der href-Wert auf .jpg, .jpeg, .png oder .gif endet
                    if (in_array($ext, ['jpg', 'jpeg', 'png', 'mp4', 'gif'])) {
                        // Wenn es ein Bild ist, ersetze das <figure>-Tag mit der aktualisierten Version
                        $updated_link = str_replace('<figure ', '<figure uk-lightbox ', $link);
                        $html = str_replace($link, $updated_link, $html);
                    }
                }
                // Setze das geänderte HTML als neues Subjekt
                $ep->setSubject($html);
            }, rex_extension::LATE);
        }
        }
    }

/** 
 * @param string $file
 * @param string $type
 *  @return string
 */
    public static function mediaCopyright(string $file = '', string $type = 'text'): string
    {
        $output = '';
        if ($file != '') {
            if ($media = rex_media::get($file)) {
                $copyright = $media->getValue('med_copyright');
                $copyright_link = $media->getValue('med_copyright_link');
                if ($copyright_link != '' && $type == 'link') {
                    $output = '<a rel="noopener" href="' . $copyright_link . '">Copyright: ' . $copyright . '</a>';
                } elseif ($copyright != '') {
                    $output = 'Copyright: ' . $copyright;
                }
            }
        }
        return $output;
    }

    /**
     * @param string $file
     * @param string $alt
     * @return string
     */
    public static function mediaAlt(string $file = '', string $alt = ''): string
    {
        $output = '';
        if ($alt != '') {
            return $alt;
        }
        if ($media = rex_media::get($file)) {
            $alt = $media->getValue('med_description');
            if ($alt !== '') {
                return $alt;
            } else {return $output;}
        }
    }

    public static function truncateText(string $string, $count = 300): string
    {
        $teaser = rex_escape(strip_tags($string));
        $teaserlen = mb_strlen($teaser);
        if ($teaserlen > $count) {
            $dotPosition = strpos($teaser, ".", $count);
            if ($dotPosition !== false) {
                // Kürzt bis zum Punkt, wenn einer nach $count gefunden wird
                $teaser = substr($teaser, 0, $dotPosition + 1);
            } else {
                // Kürzt bis zur maximalen Länge, dann weiter bis zum Ende des letzten vollständigen Wortes
                $teaser = mb_substr($teaser, 0, $count);
                $lastSpacePosition = mb_strrpos($teaser, " ");
                if ($lastSpacePosition !== false) {
                    // Kürzen Sie bis zum letzten Leerzeichen, um das letzte Wort nicht zu zerschneiden
                    $teaser = mb_substr($teaser, 0, $lastSpacePosition) . '...'; // Optional: "..." hinzufügen
                }
            }
        }
        return $teaser;
    }

    /**
     * @param string $date
     * @return string
     */
    public static function formatGermanDate(string $date): string
    {
        $formatter = new \IntlDateFormatter('de_DE', \IntlDateFormatter::LONG, \IntlDateFormatter::NONE);
        return $formatter->format(strtotime($date));
    }

    /**
     * @param string $date
     * @return string
     */
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

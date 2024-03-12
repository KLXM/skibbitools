<?php
namespace klxm\SkibbiTools;

use rex_extension;
use rex_extension_point;
use IntlDateFormatter;
use rex_media;
use rex_path;
use rex_url;

class SkibbiTools
{
    public static function cke5LightboxHelper(): void
    {
        if (rex::isFrontend()) {
            // Code hier einfügen
            rex_extension::register('OUTPUT_FILTER', function (rex_extension_point $ep) {
                $html = $ep->getSubject();

                // Verwende reguläre Ausdrücke, um verlinkte Bilder im HTML zu finden
                preg_match_all('/<figure\b[^>]*\bclass\s*=\s*["\'][^"\']*?\bimage\b[^"\']*["\'][^>]*>.*?<a[^>]+href=[\'"]([^\'"]+?\.(JPEG|JPG|GIF|PNG|jpg|jpeg|png|mp4|gif))[\'"][^>]*><img[^>]+src=[\'"]([^\'"]+?)[\'"][^>]*>.*?<\/figure>/i', $html, $matches, PREG_SET_ORDER);

                // Durchlaufe alle Treffer
                foreach ($matches as $match) {
                    // Hole die abgeglichenen Werte
                    $link = $match[0];
                    $href = $match[1];
                    $ext = strtolower($match[2]);
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
     * Liefert den alternativen Text für ein Media-Objekt aus REDAXO.
     *
     * @param string $file Der Dateiname des Media-Objekts.
     * @param string $alt Ein optionaler alternativer Text.
     * @return string Der alternative Text oder ein leerer String, wenn nicht verfügbar.
     */
    public static function mediaAlt(string $file = '', string $alt = ''): string
    {
        if ($alt !== '') {
            return $alt;
        }

        $media = rex_media::get($file);
        return $media ? $media->getValue('med_description') : '';
    }

    /**
     * Liefert den Code für die Einbindung von VTT-Dateien für Videos aus REDAXO, falls vorhanden.
     *
     * @param string $videoFile Der Dateiname des Video-Objekts im Medienpool.
     * @return string Der HTML-Code für das <track> Element mit der VTT-Datei oder ein leerer String, wenn nicht verfügbar.
     */
    public static function getVideoSubtitle(string $videoFile): string
    {
        // Ersetzt die Video-Dateiendung mit .vtt für die Untertitel-Datei
        $vttFile = preg_replace('/\.[^.]+$/', '.vtt', $videoFile);

        // Prüft, ob die VTT-Datei im Medienpool existiert
        $media = rex_media::get($vttFile);
        if ($media) {
            // Gibt den HTML-Code für das <track> Element zurück, wenn die VTT-Datei vorhanden ist
            return '<track kind="subtitles" src="/media/' . $vttFile . '" srclang="de" label="Deutsch">';
        }

        // Gibt einen leeren String zurück, wenn keine VTT-Datei vorhanden ist
        return '';
    }

    /**
     * @param string $string
     * @param int $count
     * @return string
     */
    public static function truncateText(string $string, $count = 300): string
    {
        $teaser = strip_tags($string);
        $teaserlen = mb_strlen($teaser);
        if ($teaserlen > $count) {
            $dotPosition = strpos($teaser, '.', $count);
            $questionPosition = strpos($teaser, '?', $count);
            $exclamationPosition = strpos($teaser, '!', $count);

            // Find the earliest position of either punctuation
            $positions = array_filter([$dotPosition, $questionPosition, $exclamationPosition], function ($pos) {
                return $pos !== false;
            });

            if (!empty($positions)) {
                $earliestPosition = min($positions);
                $teaser = substr($teaser, 0, $earliestPosition + 1);
            } else {
                $teaser = mb_substr($teaser, 0, $count);
                $lastSpacePosition = mb_strrpos($teaser, ' ');
                if ($lastSpacePosition !== false) {
                    $teaser = mb_substr($teaser, 0, $lastSpacePosition) . '...';
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
        $formatter = new IntlDateFormatter('de_DE', IntlDateFormatter::LONG, IntlDateFormatter::NONE);
        return $formatter->format(strtotime($date));
    }

    /**
     * @param string $date
     * @return string
     */
    public static function checkUrl(?string $url): ?string
    {
        if (!$url || filter_var($url, FILTER_VALIDATE_URL) === false) {
            return null;
        }

        if (file_exists(rex_path::media($url))) {
            return rex_url::media($url);
        } elseif (is_numeric($url)) {
            return rex_getUrl($url);
        }

        return $url;
    }

}

<?php

namespace KLXM\SkibbiTools;

use IntlDateFormatter;
use rex;
use rex_extension;
use rex_extension_point;
use rex_media;
use rex_path;
use rex_url;

use function array_filter;
use function in_array;
use function mb_strlen;
use function mb_substr;
use function min;
use function preg_match_all;
use function rex_getUrl;
use function str_replace;
use function strip_tags;
use function strpos;
use function strtotime;
use function substr;

class Tool
{
    public static function cke5LightboxHelper(): void
    {
        if (rex::isFrontend()) {
            // Register an output filter extension point
            rex_extension::register('OUTPUT_FILTER', static function (rex_extension_point $ep): void {
                $html = $ep->getSubject();

                // Use regular expressions to find linked images in the HTML
                preg_match_all('/<figure\b[^>]*\bclass\s*=\s*["\'][^"\']*?\bimage\b[^"\']*["\'][^>]*>.*?<a[^>]+href=[\'"]([^\'"]+?\.(JPEG|JPG|GIF|PNG|jpg|jpeg|png|mp4|gif))[\'"][^>]*><img[^>]+src=[\'"]([^\'"]+?)[\'"][^>]*>.*?<\/figure>/i', $html, $matches, PREG_SET_ORDER);

                // Iterate through all matches
                foreach ($matches as $match) {
                    // Get the matched values
                    $link = $match[0];
                    $href = $match[1];
                    $ext = strtolower($match[2]);
                    $src = $match[3];

                    // Check if the href value ends with .jpg, .jpeg, .png, or .gif
                    if (in_array($ext, ['jpg', 'jpeg', 'png', 'mp4', 'gif'])) {
                        // If it's an image, replace the <figure> tag with the updated version
                        $updated_link = str_replace('<figure ', '<figure uk-lightbox ', $link);
                        $html = str_replace($link, $updated_link, $html);
                    }
                }
                // Set the modified HTML as the new subject
                $ep->setSubject($html);
            }, rex_extension::LATE);
        }
    }

    public static function mediaCopyright(string $file = '', string $type = 'text'): string
    {
        $output = '';
        if ($file !== '' && ($media = rex_media::get($file))) {
            $copyright = $media->getValue('med_copyright');
            $copyright_link = $media->getValue('med_copyright_link');
            if ($copyright_link != '' && $type === 'link') {
                $output = '<a rel="noopener" href="' . $copyright_link . '">Copyright: ' . $copyright . '</a>';
            } elseif ($copyright != '') {
                $output = 'Copyright: ' . $copyright;
            }
        }
        return $output;
    }

    /**
     * Retrieves the alternative text for a media object from REDAXO.
     *
     * @param string $file the filename of the media object
     * @param string $alt an optional alternative text
     * @return string the alternative text or an empty string if not available
     */
    public static function mediaAlt(string $file = '', string $alt = ''): ?string
    {
        if ($alt !== '') {
            return $alt;
        }

        $media = rex_media::get($file);
        if ($media !== null) {
            // Make sure the value is returned as a string
            return (string) $media->getValue('med_description');
        }

        return '';
    }

    /**
     * Retrieves the code for embedding VTT files for videos from REDAXO, if available.
     *
     * @param string $videoFile the filename of the video object in the media pool
     * @return string the HTML code for the <track> element with the VTT file or an empty string if not available
     */
    public static function getVideoSubtitle(string $videoFile): string
    {
        // Replaces the video file extension with .vtt for the subtitle file
        $vttFile = preg_replace('/\.[^.]+$/', '.vtt', $videoFile);

        // Checks if the VTT file exists in the media pool
        $media = rex_media::get($vttFile);
        if ($media) {
            // Returns the HTML code for the <track> element if the VTT file exists
            return '<track kind="subtitles" src="/media/' . $vttFile . '" srclang="de" label="Deutsch">';
        }

        // Returns an empty string if no VTT file exists
        return '';
    }

    /**
     * @param int $count
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
            $positions = array_filter([$dotPosition, $questionPosition, $exclamationPosition], static function ($pos): bool {
                return $pos !== false;
            });

            if ($positions !== []) {
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

    public static function formatGermanDate(string $date): string
    {
        $timestamp = strtotime($date);
        if ($timestamp === false) {
            // Return a default value or throw an exception if the date is invalid.
            // Here, for example, return an empty string.
            return '';
        }

        $formatter = new IntlDateFormatter('de_DE', IntlDateFormatter::LONG, IntlDateFormatter::NONE);
        $formattedDate = $formatter->format($timestamp);

        // Check if the formatting was successful. IntlDateFormatter::format can return false.
        if ($formattedDate === false) {
            // Handle the error, for example, by returning a default value or throwing an exception.
            return '';
        }

        return $formattedDate;
    }

    public static function checkUrl(?string $url): ?string
    {
        if (!$url || filter_var($url, FILTER_VALIDATE_URL) === false) {
            return null;
        }

        if (file_exists(rex_path::media($url))) {
            return rex_url::media($url);
        }
        if (is_numeric($url)) {
            return rex_getUrl($url);
        }

        return $url;
    }
}

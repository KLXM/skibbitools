<?php

namespace KLXM\SkibbiTools;

use rex_extension;
use rex_extension_point;

class FocusPointLabels
{
    public function __construct()
    {
        rex_extension::register('FOCUSPOINT_PREVIEW_SELECT', function (rex_extension_point $extensionPoint): array {
            return $this->updateMediaTypesLabels($extensionPoint);
        });
    }

    /**
     * @param rex_extension_point<string> $extensionPoint
     * @return array<string, array{label: string}> a map of media types with their updated labels
     */
    public function updateMediaTypesLabels(rex_extension_point $extensionPoint): array
    {
        $mediaTypes = $extensionPoint->getSubject();
        foreach ($extensionPoint->getParams()['effectsInUse'] as $effect) {
            if (empty($effect['description'])) {
                continue;
            }
            if (!isset($mediaTypes[$effect['name']])) {
                continue;
            }
            $mediaTypes[$effect['name']]['label'] = $effect['description'];
        }
        uasort($mediaTypes, static function (array $a, array $b): int {
            return strcasecmp($a['label'], $b['label']);
        });
        return $mediaTypes;
    }
}

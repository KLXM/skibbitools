<?php

namespace KLXM\SkibbiTools;

use rex_extension;

class FocusPointLabels
{
    public function __construct()
    {
        rex_extension::register('FOCUSPOINT_PREVIEW_SELECT', function ($extensionPoint): array {
            return $this->updateMediaTypesLabels($extensionPoint);
        });
    }

    public function updateMediaTypesLabels($extensionPoint): array
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

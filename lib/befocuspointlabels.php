<?php 
namespace KLXM\SkibbiTools;

use rex_extension;

class FocusPointLabels {

    public function __construct() {
        rex_extension::register('FOCUSPOINT_PREVIEW_SELECT', [$this, 'updateMediaTypesLabels']);
    }

    public function updateMediaTypesLabels($extensionPoint): array {
        $mediaTypes = $extensionPoint->getSubject();
        foreach ($extensionPoint->getParams()['effectsInUse'] as $effect) {
            if (!empty($effect['description']) && isset($mediaTypes[$effect['name']])) {
                $mediaTypes[$effect['name']]['label'] = $effect['description'];
            }
        }
        uasort($mediaTypes, function($a, $b): int { 
            return strcasecmp($a['label'], $b['label']); 
        });
        return $mediaTypes;
    }
}

<?php

class assets_manager
{
    private static $cssFiles = [];
    private static $jsFiles = [];

    /**
     * Fügt eine CSS-Datei zur Liste hinzu und hängt den Timestamp an.
     *
     * @param string $filePath Der Pfad zur CSS-Datei relativ zum Frontend-Verzeichnis
     */
    public static function addCssFile($filePath)
    {
        $fullPath = rex_path::frontend($filePath);
        if (file_exists($fullPath)) {
            $timestamp = filemtime($fullPath);
            self::$cssFiles[] = $filePath . '?v=' . $timestamp;
        } else {
            // Fehler wird geloggt, falls die Datei nicht existiert
            rex_logger::logError(E_USER_WARNING, "CSS file not found: " . $fullPath, __FILE__, __LINE__);
        }
    }

    /**
     * Fügt eine JavaScript-Datei zur Liste hinzu und hängt den Timestamp an.
     *
     * @param string $filePath Der Pfad zur JavaScript-Datei relativ zum Frontend-Verzeichnis
     */
    public static function addJsFile($filePath)
    {
        $fullPath = rex_path::frontend($filePath);
        if (file_exists($fullPath)) {
            $timestamp = filemtime($fullPath);
            self::$jsFiles[] = $filePath . '?v=' . $timestamp;
        } else {
            // Fehler wird geloggt, falls die Datei nicht existiert
            rex_logger::logError(E_USER_WARNING, "JavaScript file not found: " . $fullPath, __FILE__, __LINE__);
        }
    }

    /**
     * Gibt alle registrierten CSS-Dateien als HTML-Links aus.
     */
    public static function renderCss()
    {
        foreach (self::$cssFiles as $file) {
            echo '<link rel="stylesheet" href="' . $file . '">' . PHP_EOL;
        }
    }

    /**
     * Gibt alle registrierten JavaScript-Dateien als HTML-Scripts aus.
     */
    public static function renderJs()
    {
        foreach (self::$jsFiles as $file) {
            echo '<script src="' . $file . '"></script>' . PHP_EOL;
        }
    }
}

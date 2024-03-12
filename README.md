# Readme für die KLXM\SkibbiTools\Tool-Klasse

Die `KLXM\SkibbiTools\Tool`-Klasse bietet eine Sammlung nützlicher Methoden für die Entwicklung mit REDAXO, darunter Funktionen für Lightbox-Integration, Urheberrechtsinformationen, alternative Texte für Medienobjekte, Video-Untertitel, Textkürzung und Datumsformatierung. Dieses Dokument erläutert, wie Sie diese Methoden in Ihren Projekten anwenden können.

## Voraussetzungen

Um die `KLXM\SkibbiTools\Tool`-Klasse nutzen zu können, müssen Sie REDAXO 5.x installiert haben. Stellen Sie sicher, dass Sie alle Abhängigkeiten installiert haben, die für die Verwendung bestimmter Methoden erforderlich sind, z. B. das `rex_media`-Objekt für Medienverwaltungsfunktionen.

## Methoden

### cke5LightboxHelper

Integriert eine Lightbox-Funktionalität für Bilder, die in einem WYSIWYG-Editor (z.B. CKEditor 5) eingefügt wurden.

#### Anwendungsbeispiel

```php
KLXM\SkibbiTools\Tool::cke5LightboxHelper();
```

Fügen Sie diese Zeile in die `boot.php` Ihres REDAXO-Projekts ein, um die Lightbox-Funktionalität automatisch zu aktivieren.

### mediaCopyright

Liefert Urheberrechtsinformationen zu einem Medienobjekt aus dem Medienpool.

#### Anwendungsbeispiel

```php
echo KLXM\SkibbiTools\Tool::mediaCopyright('mein-bild.jpg');
```

Gibt Urheberrechtsinformationen für das Bild `mein-bild.jpg` aus.

### mediaAlt

Liefert den alternativen Text für ein Media-Objekt aus dem Medienpool.

#### Anwendungsbeispiel

```php
echo KLXM\SkibbiTools\Tool::mediaAlt('mein-bild.jpg', 'Alternativer Text');
```

Gibt den alternativen Text für das Bild `mein-bild.jpg` aus. Wenn kein alternativer Text angegeben ist, wird der Beschreibungstext des Medienobjekts verwendet.

### getVideoSubtitle

Liefert den Code für die Einbindung von VTT-Dateien für Videos aus dem Medienpool.

#### Anwendungsbeispiel

```php
echo KLXM\SkibbiTools\Tool::getVideoSubtitle('mein-video.mp4');
```

Gibt den HTML-Code für das `<track>`-Element mit der VTT-Datei für das Video `mein-video.mp4` zurück.

### truncateText

Kürzt einen Text auf eine angegebene Länge und endet an einem Satzzeichen.

#### Anwendungsbeispiel

```php
echo KLXM\SkibbiTools\Tool::truncateText('Dies ist ein sehr langer Text, der gekürzt werden soll.', 50);
```

Gibt einen gekürzten Text zurück, der auf 50 Zeichen begrenzt ist und an einem Satzzeichen endet.

### formatGermanDate

Formatiert ein Datum im deutschen Format.

#### Anwendungsbeispiel

```php
echo KLXM\SkibbiTools\Tool::formatGermanDate('2023-03-10');
```

Gibt das Datum `10. März 2023` aus.

### checkUrl

Überprüft eine URL auf Gültigkeit und gibt die korrekte URL zurück, falls vorhanden.

#### Anwendungsbeispiel

```php
echo KLXM\SkibbiTools\Tool::checkUrl('https://example.com');
```

Gibt die überprüfte URL zurück oder `null`, wenn die URL ungültig ist.

## Fazit

Die `KLXM\SkibbiTools\Tool`-Klasse bietet vielseitige Werkzeuge für die Entwicklung mit REDAXO. Durch die Verwendung dieser Methoden können Sie Ihre Entwicklungsarbeit effizienter gestalten und die Benutzerfreundlichkeit Ihrer Projekte verbessern.

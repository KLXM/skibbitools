# SkibbiTools

Die `SkibbiTools`-Klasse bietet eine Sammlung von nützlichen Hilfsmethoden für die Entwicklung von REDAXO-Webseiten. Diese Methoden umfassen Funktionen zur Arbeit mit Medien, Texten, Daten und URLs, die speziell für die Anforderungen von REDAXO-Projekten angepasst sind.

## Funktionen

### cke5LightboxHelper

Fügt Unterstützung für Lightboxen zu Bildern hinzu, die über den CKEditor 5 eingefügt wurden. Diese Methode sollte im Frontend aufgerufen werden, um automatisch `uk-lightbox` Attribute zu `<figure>` Tags hinzuzufügen, die Bilder umschließen.

```php
SkibbiTools::cke5LightboxHelper();
```

### mediaCopyright

Gibt den Urheberrechtshinweis eines Medienobjekts zurück. Unterstützt die Rückgabe als Text oder als Link.

```php
echo SkibbiTools::mediaCopyright('bild.jpg', 'text');
```

### mediaAlt

Liefert den alternativen Text für ein Medienobjekt aus dem REDAXO-Medienpool.

```php
echo SkibbiTools::mediaAlt('bild.jpg');
```

### getVideoSubtitle

Ermittelt, ob zu einem Video eine VTT-Datei im Medienpool existiert und gibt den entsprechenden HTML-Code für das `<track>`-Element zurück.

```php
echo SkibbiTools::getVideoSubtitle('video.mp4');
```

### truncateText

Kürzt einen Text auf eine angegebene Länge und versucht, am Ende eines Satzes oder Wortes zu enden, um die Lesbarkeit zu erhalten.

```php
echo SkibbiTools::truncateText('Dies ist ein langer Beispieltext, der gekürzt werden soll.', 100);
```

### formatGermanDate

Formattiert ein Datum im deutschen Format.

```php
echo SkibbiTools::formatGermanDate('2020-01-01');
```

### checkUrl

Überprüft eine URL auf Gültigkeit und wandelt sie gegebenenfalls in eine gültige REDAXO-URL um.

```php
echo SkibbiTools::checkUrl('https://example.com');
```

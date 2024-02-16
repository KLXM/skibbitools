# SkibbiTools
Skibbi's experiments for REDAXO cms

Inhalt: 

- Class skibbiTools

## Class SkibbiTools 

Die Klasse `skibbiTools` stellt eine Sammlung von statischen Hilfsfunktionen bereit, die in REDAXO 5.x Projekten verwendet werden können. Diese Funktionen bieten verschiedene Funktionalitäten, von der Bearbeitung von HTML-Inhalten bis hin zur Formatierung und Validierung von Daten.

#### `cke5LightboxHelper()`

- **Beschreibung:** Diese Methode modifiziert das HTML von Seiten, um Lightbox-Funktionalität für Bilder und Videos zu ermöglichen. Sie wird im Kontext des `OUTPUT_FILTER`-Extensionspunktes von REDAXO angewendet.
- **Parameter:** Keine
- **Rückgabe:** void
- **Details:** 
  - Überprüft, ob die Ausführung im Frontend stattfindet.
  - Nutzt reguläre Ausdrücke, um verlinkte Bilder und Videos im HTML zu identifizieren.
  - Fügt das Attribut `uk-lightbox` zu `<figure>`-Tags hinzu, um Lightbox-Funktionalität zu ermöglichen.

#### `mediaCopyright(string $file = '', string $type = 'text'): string`

- **Beschreibung:** Gibt den Urheberrechtshinweis für ein spezifisches Media-Objekt zurück.
- **Parameter:**
  - `$file`: Der Name der Mediendatei.
  - `$type`: Der Typ der Ausgabe, 'text' oder 'link'.
- **Rückgabe:** Ein String, der den Urheberrechtshinweis enthält, entweder als reiner Text oder als Link.

#### `truncateText(string $string, int $length = 300): string`

- **Beschreibung:** Kürzt einen Text auf eine bestimmte Länge und beendet ihn am nächsten Satzende.
- **Parameter:**
  - `$string`: Der zu kürzende Text.
  - `$length`: Die maximale Länge des Textes.
- **Rückgabe:** Der gekürzte Text.

#### `formatGermanDate(string $date): string`

- **Beschreibung:** Formatiert ein Datum im deutschen Format.
- **Parameter:**
  - `$date`: Das zu formatierende Datum.
- **Rückgabe:** Das formatierte Datum im deutschen Format.

#### `checkUrl(?string $url): ?string`

- **Beschreibung:** Überprüft und korrigiert URLs. Es wird geprüft, ob die URL gültig ist, und bei Bedarf wird eine korrekte URL zurückgegeben.
- **Parameter:**
  - `$url`: Die zu überprüfende URL.
- **Rückgabe:** Eine korrigierte und überprüfte URL oder `null`, falls die URL ungültig ist.

### Hinweise

Die Methoden dieser Klasse sind so konzipiert, dass sie spezifische Aufgaben im Kontext von REDAXO-Projekten erfüllen. Sie sollten im Rahmen der Entwicklung von REDAXO-Websites oder -Anwendungen verwendet werden, um konsistente und effiziente Lösungen zu gewährleisten.



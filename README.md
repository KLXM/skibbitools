# Skibbi's tools
Skibbi's experiments for REDAXO cms

Inhalt: 

- Class skibbiTools
- Class skOrm 

## Class skibbiTools 

### `skibbiTools`

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



## skOrm

Die `skOrm`-Klasse dient als objektorientierte Abstraktionsschicht für Datenbankoperationen in REDAXO-Projekten. Sie vereinfacht die Interaktion mit der Datenbank, indem sie komplexe SQL-Abfragen und Operationen hinter einer klaren und intuitiven API verbirgt. Diese Klasse ermöglicht es Entwicklern, effizienter und fehlerfreier mit der Datenbank zu interagieren, ohne sich um die Details der zugrunde liegenden SQL-Syntax kümmern zu müssen.

## Einfaches Anwendungsbeispiel

### Einsatz der `findAll`-Methode

#### Mit `skOrm`:
```php
$orm = new skOrm('nutzer');
$alleNutzer = $orm->findAll();
```
Diese Methode ruft alle Datensätze aus der Tabelle `nutzer` ab.

#### Alternative mit `rex_sql::factory`:
```php
$sql = rex_sql::factory();
$sql->setQuery('SELECT * FROM nutzer');
$alleNutzer = $sql->getArray();
```

## Methodenübersicht und Beispiele

### CRUD-Operationen

#### 1. Einfügen (`insert`)
Ermöglicht das Einfügen neuer Datensätze.

##### Beispiel:
```php
$orm = new skOrm('nutzer');
$neueId = $orm->insert(['name' => 'Max', 'email' => 'max@example.com']);
```
Mit `rex_sql::factory`:
```php
$sql = rex_sql::factory();
$sql->setTable('nutzer');
$sql->setValue('name', 'Max');
$sql->setValue('email', 'max@example.com');
$sql->insert();
```

#### 2. Lesen (`load`, `findAll`, `getOne`)
Zum Abrufen von Datensätzen aus der Datenbank.

##### Beispiel:
```php
$orm = new skOrm('nutzer');
$nutzer = $orm->load(1); // Lädt Nutzer mit ID 1
```
Mit `rex_sql::factory`:
```php
$sql = rex_sql::factory();
$sql->setQuery('SELECT * FROM nutzer WHERE id = 1');
$nutzer = $sql->getArray()[0];
```

#### 3. Aktualisieren (`update`)
Aktualisiert vorhandene Datensätze.

##### Beispiel:
```php
$orm = new skOrm('nutzer');
$orm->where('id', '=', 1)->update(['name' => 'Maximilian']);
```
Mit `rex_sql::factory`:
```php
$sql = rex_sql::factory();
$sql->setTable('nutzer');
$sql->setWhere(['id' => 1]);
$sql->setValue('name', 'Maximilian');
$sql->update();
```

#### 4. Löschen (`delete`)
Entfernt Datensätze aus der Datenbank.

##### Beispiel:
```php
$orm = new skOrm('nutzer');
$orm->where('id', '=', 1)->delete();
```
Mit `rex_sql::factory`:
```php
$sql = rex_sql::factory();
$sql->setTable('nutzer');
$sql->setWhere(['id' => 1]);
$sql->delete();
```

### Weitere Methoden

#### 1. Suchen und Ersetzen (`searchAndReplace`)
Ermöglicht das Suchen und Ersetzen von Daten in der Datenbank.

##### Beispiel:
```php
$orm = new skOrm('nutzer');
$orm->searchAndReplace(['email'], '@alte-domain.de', '@neue-domain.de');
```
Mit `rex_sql::factory` wäre dies komplexer und erfordert manuelle Iteration und Update für jeden Datensatz.

#### 2. Paginierung (`paginate`)
Unterstützt das Paginieren von Daten.

##### Beispiel:
```php
$orm = new skOrm('nutzer');
$ergebnisse = $orm->paginate(2, 10); // Seite 2, 10 Einträge pro Seite
```
Mit `rex_sql::factory` muss dies manuell durch Berechnung des Offsets und Limits in der Abfrage erfolgen.

#### 3. Spaltenzusammenführung (`concatFields`)
Zum Kombinieren mehrerer Spalten oder Werte in einem SQL-Query.

##### Beispiel:
```php
$orm = new skOrm('nutzer');
$orm->concatFields(['vorname', '{ }', 'nachname'], 'voller_name')->get();
```
Mit `rex_sql::factory` müsste dies direkt in der SQL-Abfrage formuliert werden, z.B. durch manuelles Schreiben der `CONCAT`-Funktion.

## Umfangreiches Fallbeispiel

Ein umfangreiches Beispiel, das mehrere Methoden kombiniert, könnte wie folgt aussehen:

```php
$orm = new skOrm('produkte');
$orm->where('kategorie', '=', 'Bücher')
    ->orderBy('preis', 'ASC')
    ->limit(10)
    ->concatFields(['titel', '{ - }', 'autor'], 'produkt_info');

$produkte = $orm->get();

foreach ($produkte as $produkt) {
    echo "Produkt: {$produkt['produkt_info']}, Preis: {$produkt['preis']}\n";
}
```

In diesem Beispiel:
- Wir filtern Produkte in der Kategorie 'Bücher'.
- Sortieren diese nach Preis aufsteigend.
- Begrenzen die Ergebnisse auf 10 Einträge.
- Fügen `titel` und `autor` zu einem Feld `produkt_info` zusammen.
- Das Ergebnis ist eine Liste von Produkten mit kombinierten Informationen und Preis.

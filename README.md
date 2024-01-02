# Skibbi's tools
Skibbi's experiments for REDAXO cms

## skOrm

Die Klasse `skOrm` ist ein Objekt-Relationaler Mapper (ORM), der für die Interaktion mit Datenbanken in REDAXO 5.x verwendet werden kann. Sie bietet Methoden zur Verwaltung von Datenbankoperationen wie Abfragen, Einfügen, Aktualisieren und Löschen von Datensätzen.

### Konstruktor

- `__construct(string $tableName)`: Initialisiert eine Instanz der Klasse mit dem angegebenen Tabellennamen.

  ```php
  $orm = new skOrm('meine_tabelle');
  ```

### Methoden

#### Datenbank-Index setzen

- `setDbIndex(int $dbIndex): self`: Setzt den Index der Datenbankverbindung.

  ```php
  $orm->setDbIndex(2);
  ```

#### Datensätze laden

- `load(int $id): ?array`: Lädt einen Datensatz anhand seiner ID.

  ```php
  $datensatz = $orm->load(1);
  ```

- `findAll(): array`: Lädt alle Datensätze aus der Tabelle.

  ```php
  $alleDatensaetze = $orm->findAll();
  ```

- `getOne(): ?array`: Lädt den ersten Datensatz, der den Abfragekriterien entspricht.

  ```php
  $ersterDatensatz = $orm->where('name', '=', 'Max')->getOne();
  ```

#### Suche und Ersetzen

- `searchAndReplace(array $columns, string $search, string $replace, bool $testOnly = false): array`: Sucht und ersetzt in angegebenen Spalten.

  ```php
  $betroffeneIds = $orm->searchAndReplace(['name', 'beschreibung'], 'alt', 'neu');
  ```

#### Zählen

- `count(): int`: Zählt die Anzahl der Datensätze, die den Kriterien entsprechen.

  ```php
  $anzahl = $orm->where('status', '=', 'aktiv')->count();
  ```

#### Einfügen und Aktualisieren

- `insert(array $data): int`: Fügt einen neuen Datensatz ein.

  ```php
  $neueId = $orm->insert(['name' => 'Neuer Name', 'status' => 'aktiv']);
  ```

- `update(array $data): void`: Aktualisiert Datensätze.

  ```php
  $orm->where('id', '=', 1)->update(['name' => 'Geänderter Name']);
  ```

#### Löschen

- `delete(): void`: Löscht Datensätze.

  ```php
  $orm->where('id', '=', 1)->delete();
  ```

#### Bedingungen und Sortierung

- `where(string $column, string $operator, $value): self`: Fügt eine Bedingung hinzu.

  ```php
  $orm->where('status', '=', 'aktiv');
  ```

- `orderBy(string $column, string $direction = 'ASC'): self`: Fügt eine Sortierungsbedingung hinzu.

  ```php
  $orm->orderBy('name', 'DESC');
  ```

#### Auswahl spezifischer Spalten

- `select(array $columns): self`: Wählt spezifische Spalten für die Abfrage aus.

  ```php
  $orm->select(['name', 'status']);
  ```

#### Beziehungen

- `with(string $relationName, string $foreignTable, string $foreignKey, string $localKey = 'id'): self`: Definiert eine Beziehung zu einer anderen Tabelle.

  ```php
  $orm->with('benutzer', 'benutzer_tabelle', 'benutzer_id');
  ```

### Anwendung in REDAXO 5.x

Die `skOrm`-Klasse bietet eine flexible und intuitive Möglichkeit, Datenbankoperationen in REDAXO 5.x zu handhaben. Sie erleichtert die Entwicklung durch die Bereitstellung einer klaren API für häufige Datenbankaktionen.


## Beispiele: 

Natürlich, hier sind vier praktische Fallbeispiele zur Verwendung der `skOrm`-Klasse in verschiedenen Szenarien:

### Fallbeispiel 1: Datensatz Abrufen
Angenommen, Sie möchten einen bestimmten Datensatz aus der Tabelle `mitarbeiter` anhand seiner ID abrufen.

```php
$orm = new skOrm('mitarbeiter');
$mitarbeiter = $orm->load(5); // Angenommen, die ID des Mitarbeiters ist 5

if ($mitarbeiter) {
    echo 'Mitarbeiter gefunden: ' . $mitarbeiter['name'];
} else {
    echo 'Kein Mitarbeiter mit dieser ID gefunden.';
}
```

### Fallbeispiel 2: Mehrere Datensätze mit Bedingungen und Sortierung
Sie möchten alle aktiven Projekte aus der `projekte` Tabelle abrufen und diese nach dem Startdatum absteigend sortieren.

```php
$orm = new skOrm('projekte');
$aktiveProjekte = $orm->where('status', '=', 'aktiv')
                      ->orderBy('startdatum', 'DESC')
                      ->get();

foreach ($aktiveProjekte as $projekt) {
    echo 'Projekt: ' . $projekt['name'] . ', Startdatum: ' . $projekt['startdatum'] . "\n";
}
```

### Fallbeispiel 3: Datensatz Einfügen
Sie möchten einen neuen Mitarbeiter in die `mitarbeiter` Tabelle einfügen.

```php
$orm = new skOrm('mitarbeiter');
$neueMitarbeiterId = $orm->insert([
    'name' => 'Max Mustermann',
    'abteilung' => 'Entwicklung',
    'status' => 'aktiv'
]);

echo 'Neuer Mitarbeiter hinzugefügt mit ID: ' . $neueMitarbeiterId;
```

### Fallbeispiel 4: Datensätze Aktualisieren und Löschen
Sie möchten den Status eines Mitarbeiters aktualisieren und inaktive Mitarbeiter aus der Tabelle entfernen.

```php
// Mitarbeiterstatus aktualisieren
$orm = new skOrm('mitarbeiter');
$orm->where('id', '=', 3)->update(['status' => 'inaktiv']);

// Inaktive Mitarbeiter löschen
$orm->where('status', '=', 'inaktiv')->delete();
echo 'Inaktive Mitarbeiter wurden gelöscht.';
```


### Fallbeispiel 5: Abrufen von Artikeln mit Autor-Informationen

Zuerst wird die Relation in der `skOrm`-Klasse definiert, indem die Methode `with()` verwendet wird. Anschließend kann man die Artikel abrufen und für jeden Artikel die zugehörigen Autorendaten anzeigen.

```php
$orm = new skOrm('artikel');
$orm->with('autor', 'rex_user', 'id', 'autor_id'); // Relation definiert

$artikelMitAutoren = $orm->get();

foreach ($artikelMitAutoren as $artikel) {
    echo 'Titel: ' . $artikel['titel'] . "\n";
    echo 'Inhalt: ' . $artikel['inhalt'] . "\n";
    echo 'Autor: ' . $artikel['autor'][0]['name'] . "\n"; // Zugriff auf die Autorendaten
    echo "---------------------\n";
}
```

In diesem Beispiel:

- `with('autor', 'rex_user', 'id', 'autor_id')`: Diese Zeile definiert die Relation. Der erste Parameter `autor` ist der Name der Relation, `rex_user` ist der Name der verknüpften Tabelle, `id` ist der Primärschlüssel in der `rex_user`-Tabelle und `autor_id` ist der Fremdschlüssel in der `artikel`-Tabelle.
- `findAll()`: Diese Methode ruft alle Artikel aus der `artikel`-Tabelle ab. Dank der definierten Relation werden auch die entsprechenden Autorendaten geladen.
- Innerhalb der `foreach`-Schleife greifen Sie auf die Artikel- und Autorendaten zu. Beachten Sie, dass die Autorendaten als Array von Arrays vorliegen, daher verwenden Sie `[0]` um auf den ersten (und in diesem Fall einzigen) Autor zuzugreifen.

Dieses Beispiel demonstriert, wie die `skOrm`-Klasse verwendet werden kann, um Beziehungen zwischen Tabellen in REDAXO zu handhaben und abzufragen.

# Skibbi's tools
Skibbi's experiments for REDAXO cms

## skOrm

Die Klasse `skOrm` ist ein benutzerdefinierter Objekt-Relationaler Mapper (ORM), der für die Interaktion mit Datenbanken in REDAXO 5.x verwendet werden kann. Sie bietet eine Vielzahl von Methoden zur Verwaltung von Datenbankoperationen wie Abfragen, Einfügen, Aktualisieren und Löschen von Datensätzen. Hier ist eine detaillierte Dokumentation mit Beispielen für die wichtigsten Methoden der Klasse `skOrm`.

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

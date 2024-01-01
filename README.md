# Skibbi's tools
Skibbi's experiments for REDAXO cms


## skOrm-Class

### Dokumentation für die `skOrm` Klasse

- Diese Klasse nutzt die `rex_sql` Klasse von REDAXO für Datenbankoperationen.
- Sie implementiert grundlegende ORM-Funktionalitäten wie Laden, Einfügen, Aktualisieren und Löschen von Datensätzen.
- Es können auch komplexere Abfragen durch Kombination von Methoden wie `where` und `get` erstellt werden.
- Die `with` Methode ermöglicht einfache relationale Abfragen.

  
#### Konstruktor

```php
public function __construct(string $tableName)
```
- `tableName`: Der Name der Datenbanktabelle, mit der das Objekt arbeiten soll.

#### Methoden

##### `load`
```php
public function load(int $id): ?array
```
- Lädt einen Datensatz anhand seiner ID.
- Gibt den Datensatz als Array zurück oder `null`, falls kein Datensatz gefunden wird.

##### `findAll`
```php
public function findAll(): array
```
- Ruft alle Datensätze aus der Tabelle ab.
- Gibt ein Array von Datensätzen zurück.

##### `findBy`
```php
public function findBy(array $criteria): array
```
- Sucht Datensätze basierend auf angegebenen Kriterien.
- `criteria`: Ein Array von Bedingungen (Schlüssel-Wert-Paare).
- Gibt ein Array von Datensätzen zurück, die den Kriterien entsprechen.

##### `findOneBy`
```php
public function findOneBy(array $criteria): ?array
```
- Ähnlich wie `findBy`, aber gibt nur den ersten passenden Datensatz zurück.

##### `countBy`
```php
public function countBy(array $criteria): int
```
- Zählt, wie viele Datensätze den angegebenen Kriterien entsprechen.

##### `insert`
```php
public function insert(array $data): int
```
- Fügt einen neuen Datensatz in die Tabelle ein.
- `data`: Ein Array von Daten, die eingefügt werden sollen.
- Gibt die ID des eingefügten Datensatzes zurück.

##### `update`
```php
public function update(array $data, array $criteria): void
```
- Aktualisiert Datensätze, die den angegebenen Kriterien entsprechen.
- `data`: Die zu aktualisierenden Daten.

##### `delete`
```php
public function delete(array $criteria): void
```
- Löscht Datensätze, die den angegebenen Kriterien entsprechen.

##### `where`
```php
public function where(string $column, string $operator, $value): self
```
- Fügt eine WHERE-Bedingung für die nächste Abfrage hinzu.

##### `whereRaw`
```php
public function whereRaw(string $rawCondition, array $bindings = []): self
```
- Ermöglicht es, eine rohe WHERE-Bedingung mit Bindungen hinzuzufügen.

##### `get`
```php
public function get(): array
```
- Führt die zusammengesetzte Abfrage aus und gibt die Ergebnisse zurück.

##### `resetQuery`
```php
public function resetQuery(): void
```
- Setzt die Abfragebedingungen zurück.

##### `with`
```php
public function with(string $relationName, string $foreignTable, string $foreignKey, string $localKey = 'id'): self
```
- Definiert eine Relation zu einer anderen Tabelle.

### Beispiele

```php
$skOrm = new skOrm('meine_tabelle');
$datensatz = $skOrm->load(1);
$alleDatensätze = $skOrm->findAll();
```


### Beispiel 1: Laden und Anzeigen von Daten

Angenommen, Sie haben ein REDAXO-AddOn, das Informationen zu Veranstaltungen verwaltet. Die Informationen werden in einer Tabelle `events` gespeichert. Um Daten zu laden und anzuzeigen, können Sie folgendermaßen vorgehen:

```php
// Initialisierung der skOrm Klasse für die 'events' Tabelle
$eventOrm = new skOrm('events');

// Laden eines spezifischen Events anhand der ID
$event = $eventOrm->load(1);

// Überprüfen, ob das Event existiert und Anzeigen der Informationen
if ($event !== null) {
    echo "Event: " . $event['name'] . ", Datum: " . $event['date'];
}
```

### Beispiel 2: Erstellen eines neuen Datensatzes

Um ein neues Event hinzuzufügen, können Sie die `insert` Methode verwenden:

```php
// Daten für das neue Event
$newEventData = [
    'name' => 'Sommerfest',
    'date' => '2024-07-15'
];

// Einfügen des neuen Events
$eventId = $eventOrm->insert($newEventData);

echo "Neues Event mit der ID $eventId hinzugefügt.";
```

### Beispiel 3: Aktualisieren von Datensätzen

Um ein bestehendes Event zu aktualisieren, können Sie die `update` Methode nutzen:

```php
// Daten für das Update
$updateData = ['name' => 'Winterfest'];

// Kriterien für das zu aktualisierende Event
$criteria = ['id' => 2];

// Aktualisieren des Events
$eventOrm->update($updateData, $criteria);

echo "Event aktualisiert.";
```

### Beispiel 4: Löschen eines Datensatzes

Um ein Event zu löschen, verwenden Sie die `delete` Methode:

```php
// Kriterien für das zu löschende Event
$criteria = ['id' => 3];

// Löschen des Events
$eventOrm->delete($criteria);

echo "Event gelöscht.";
```

### Beispiel 5: Abrufen mehrerer Datensätze mit Bedingungen

Um mehrere Events basierend auf bestimmten Kriterien zu erhalten, verwenden Sie die `findBy` Methode:

```php
// Kriterien für die zu findenden Events
$criteria = ['location' => 'Berlin'];

// Finden von Events, die den Kriterien entsprechen
$events = $eventOrm->findBy($criteria);

foreach ($events as $event) {
    echo "Event: " . $event['name'] . ", Ort: " . $event['location'] . "<br>";
}
```

### Beispiel 4: RAW-Abfrage

Um alle Termine zwischen dem 1. Januar 2024 und dem 1. Juni 2024 abzurufen, können Sie die `whereRaw` Methode zusammen mit der `get` Methode der `skOrm` Klasse verwenden. Diese Methode ermöglicht es Ihnen, eine benutzerdefinierte SQL-Bedingung zu formulieren, die es erlaubt, einen Datumsbereich zu spezifizieren. Hier ist ein Beispiel, wie Sie dies umsetzen können:

```php
// Initialisierung der skOrm Klasse für die 'events' Tabelle
$eventOrm = new skOrm('events');

// Formulieren der Bedingung für den Zeitraum
$startDate = '2024-01-01';
$endDate = '2024-06-01';
$rawCondition = 'date >= :startDate AND date <= :endDate';

// Hinzufügen der Bedingung und der Parameter zur Abfrage
$eventOrm->whereRaw($rawCondition, ['startDate' => $startDate, 'endDate' => $endDate]);

// Ausführen der Abfrage und Erhalten der Ergebnisse
$events = $eventOrm->get();

// Anzeigen der Ergebnisse
foreach ($events as $event) {
    echo "Event: " . $event['name'] . ", Datum: " . $event['date'] . "<br>";
}
```

In diesem Beispiel:

- `whereRaw` wird verwendet, um eine benutzerdefinierte Bedingung zu setzen, die den gewünschten Datumsbereich abdeckt.
- `:startDate` und `:endDate` sind Platzhalter für die Parameter, die in der Abfrage verwendet werden.
- Die Methode `get` führt die Abfrage aus und gibt die Ergebnisse zurück.


### Beispiel 5: Relationen auflösen 

Um in REDAXO mit der `skOrm` Klasse eine Relation zwischen Veranstaltungen (in der `events` Tabelle) und deren Autoren (in der REDAXO-internen `user` Tabelle) aufzulösen, nutzen Sie die `with` Methode, um eine Verknüpfung zwischen diesen Tabellen herzustellen. Hierbei wird angenommen, dass die `events` Tabelle eine Spalte `author_id` enthält, die auf die ID eines Nutzers in der `user` Tabelle verweist.

### Schritte zur Auflösung der Relation:

1. **Definieren der Relation:** 
   Verwenden Sie die `with` Methode, um die Relation zwischen `events` und `user` zu definieren. Hierbei wird angegeben, dass die `author_id` in `events` mit der `id` in `user` korrespondiert.

   ```php
   $eventOrm = new skOrm('events');
   $eventOrm->with('author', 'rex_user', 'id', 'author_id');
   ```

2. **Abrufen und Anzeigen der Daten:**
   Verwenden Sie die `get` Methode, um die Events zusammen mit den Autorendaten abzurufen. Die Autorendaten werden im resultierenden Array unter dem Schlüssel `'author'` verfügbar sein.

   ```php
   $events = $eventOrm->get();
   foreach ($events as $event) {
       echo "Event: " . $event['name'] . ", Datum: " . $event['date'];
       echo "<br>Autor: " . $event['author']['name'] . "<br><br>";
   }
   ```

In diesem Szenario kümmert sich `skOrm` um die Verknüpfung der Daten aus beiden Tabellen, sodass Sie einfach auf die Autorendaten für jedes Event zugreifen können.





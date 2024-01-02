# Skibbi's tools
Skibbi's experiments for REDAXO cms

## skOrm

### Kleises experimentelles ORM für REDAXO rex_sql

#### Kategorisierung der Methoden
1. **Initialisierung und Konfiguration**
2. **CRUD-Operationen**
3. **Such- und Filtermethoden**
4. **Aggregations- und Hilfsmethoden**
5. **Relationen und Joins**
6. **Query-Building und Modifikationen**

#### 1. Initialisierung und Konfiguration
- **Konstruktor (`__construct`)**: Initialisiert die Klasse mit dem Namen der Datenbanktabelle.
  ```php
  $orm = new skOrm('meine_tabelle');
  ```
- **Datenbank-Index setzen (`setDbIndex`)**: Legt den Index der zu verwendenden Datenbank fest.
  ```php
  $orm->setDbIndex(2);
  ```

#### 2. CRUD-Operationen
- **Laden (`load`)**: Lädt einen Datensatz anhand seiner ID.
  ```php
  $daten = $orm->load(1);
  ```
- **Alle finden (`findAll`)**: Ruft alle Datensätze aus der Tabelle ab.
  ```php
  $alleDaten = $orm->findAll();
  ```
- **Einfügen (`insert`)**: Fügt einen neuen Datensatz in die Tabelle ein.
  ```php
  $id = $orm->insert(['spalte1' => 'wert1', 'spalte2' => 'wert2']);
  ```
- **Aktualisieren (`update`)**: Aktualisiert Datensätze basierend auf festgelegten Bedingungen.
  ```php
  $orm->where('id', '=', 1)->update(['spalte1' => 'neuerWert']);
  ```
- **Löschen (`delete`)**: Löscht Datensätze basierend auf festgelegten Bedingungen.
  ```php
  $orm->where('id', '=', 1)->delete();
  ```

#### 3. Such- und Filtermethoden
- **Bedingungen setzen (`where`, `whereRaw`, `whereInList`)**: Ermöglicht das Filtern von Daten anhand spezifischer Bedingungen.
  ```php
  $gefilterteDaten = $orm->where('spalte', '=', 'wert')->get();
  ```
- **Suchen und Ersetzen (`searchAndReplace`)**: Sucht nach einem String in bestimmten Spalten und ersetzt ihn.
  ```php
  $betroffeneZeilen = $orm->searchAndReplace(['spalte1'], 'suche', 'ersetze');
  ```
- **Nach String suchen (`searchByString`)**: Findet Datensätze, die einen bestimmten String enthalten.
  ```php
  $suchergebnisse = $orm->searchByString(['spalte1', 'spalte2'], 'suchString');
  ```

#### 4. Aggregations- und Hilfsmethoden
- **Zählen (`count`)**: Zählt die Anzahl der Datensätze, die den festgelegten Bedingungen entsprechen.
  ```php
  $anzahl = $orm->where('spalte', '=', 'wert')->count();
  ```
- **Paginierung (`paginate`)**: Unterstützt das Paginieren von Datensätzen.
  ```php
  $seitenDaten = $orm->paginate(1, 10);
  ```

#### 5. Relationen und Joins
- **Relationen hinzufügen (`with`)**: Bindet verwandte Datensätze aus einer anderen Tabelle ein.
  ```php
  $datenMitRelation = $orm->with('relationName', 'andere_tabelle', 'fremdschluessel')->get();
  ```
- **Join-Methoden (`innerJoin`, `leftJoin`, `rightJoin`)**: Erlauben die Kombination von Daten aus verschiedenen Tabellen.
  ```php
  $ergebnisse = $orm->select(['t1.spalte', 't2.andereSpalte'])->leftJoin('andere_tabelle', 't1.id = t2.foreign_id')->get();
  ```

#### 6. Query-Building und Modifikationen
- **Selektieren (`select`)**: Spezifiziert, welche Spalten abgerufen werden sollen.
  ```php
  $spezifischeDaten = $orm->select(['spalte1', 'spalte2'])->get();
  ```
- **Sortierung (`orderBy`)**: Sortiert die Ergebnisse anhand einer bestimmten Spalte.
  ```php
  $sortierteDaten = $orm->orderBy('spalte', 'DESC')->get();
  ```
- **Limit und Offset setzen (`limit`)**: Begrenzt die Anzahl der zurückgegebenen Datensätze.
  ```php
  $limitierteDaten = $orm->limit(10, 5)->get();
  ```

## Praxisbeispiele

#### Beispiel 1: Einfaches Abrufen von Daten
```php
$orm = new skOrm('artikel');
$alleArtikel = $orm->findAll();
foreach ($alleArtikel as $artikel) {
    echo "Artikel-ID: {$artikel['id']}, Titel: {$artikel['titel']}\n";
}
```
*Beschreibung*: Dieses Beispiel zeigt, wie man alle Datensätze aus der Tabelle `artikel` abruft und durchläuft.

#### Beispiel 2: Gezielte Abfrage mit Bedingungen
```php
$orm = new skOrm('nutzer');
$aktiveNutzer = $orm->where('status', '=', 'aktiv')->get();
foreach ($aktiveNutzer as $nutzer) {
    echo "Nutzer-ID: {$nutzer['id']}, Name: {$nutzer['name']}\n";
}
```
*Beschreibung*: Hier werden Nutzer gefiltert, die den Status `aktiv` haben.

#### Beispiel 3: Kombinieren von Daten mit Join
```php
$orm = new skOrm('bestellungen');
$bestellungenMitKunden = $orm->select(['bestellungen.id', 'kunden.name'])
                             ->leftJoin('kunden', 'bestellungen.kunde_id = kunden.id')
                             ->get();
foreach ($bestellungenMitKunden as $bestellung) {
    echo "Bestell-ID: {$bestellung['id']}, Kunde: {$bestellung['name']}\n";
}
```
*Beschreibung*: In diesem Beispiel werden Bestellungen mit den dazugehörigen Kundennamen durch einen Left Join abgerufen.

#### Beispiel 4: Paginierung von Datensätzen
```php
$orm = new skOrm('produkte');
$seite = 2;
$produkteProSeite = 10;
$paginierteProdukte = $orm->paginate($seite, $produkteProSeite);
echo "Seite {$seite} von {$paginierteProdukte['lastPage']}\n";
foreach ($paginierteProdukte['data'] as $produkt) {
    echo "Produkt-ID: {$produkt['id']}, Name: {$produkt['name']}\n";
}
```
*Beschreibung*: Dieses Beispiel zeigt, wie man Produkte paginiert abfragt. Hier werden die Produkte auf der zweiten Seite mit 10 Produkten pro Seite dargestellt.

#### Beispiel 5: Aktualisieren von Datensätzen
```php
$orm = new skOrm('mitarbeiter');
$orm->where('abteilung', '=', 'Marketing')
    ->update(['status' => 'im Urlaub']);
echo "Alle Mitarbeiter in der Marketing-Abteilung sind nun im Urlaub.";
```
*Beschreibung*: Hier werden alle Mitarbeiter der Marketing-Abteilung auf den Status `im Urlaub` gesetzt.


#### Beispiel 6 : Aktualisieren von E-Mail-Adressen
In diesem Beispiel wird die Methode `searchAndReplace` verwendet, um in der Tabelle `nutzer` alle E-Mail-Adressen zu aktualisieren, die mit einer bestimmten Domain enden.

```php
$orm = new skOrm('nutzer');
$alteDomain = '@alte-domain.de';
$neueDomain = '@neue-domain.de';

// Suche nach Nutzern mit der alten Domain und ersetze sie durch die neue Domain
$betroffeneNutzer = $orm->searchAndReplace(['email'], $alteDomain, $neueDomain);

echo "Aktualisierte Nutzer-IDs: " . implode(', ', $betroffeneNutzer) . "\n";
```

*Beschreibung*: 
- Die Methode `searchAndReplace` wird aufgerufen, wobei als Parameter die zu durchsuchende Spalte (`email`), der zu suchende String (`@alte-domain.de`) und der Ersatzstring (`@neue-domain.de`) übergeben werden.
- Die Methode durchsucht alle `email`-Spaltenwerte in der Tabelle `nutzer`, findet diejenigen, die `@alte-domain.de` enthalten, und ersetzt diesen Teil des Strings durch `@neue-domain.de`.
- Die IDs der betroffenen Nutzer werden zurückgegeben und ausgegeben.

### Zusammenfassung
Dieses Beispiel zeigt, wie man die `searchAndReplace`-Methode der `skOrm`-Klasse nutzen kann, um spezifische Teile von Daten in einer Datenbanktabelle zu suchen und zu ersetzen. Es ist besonders nützlich für Massenaktualisierungen, wie z.B. das Ändern von E-Mail-Domainnamen in Nutzerdaten.

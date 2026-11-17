# CacheTrail -- Projekt- und Entwicklungsdokumentation

**Version:** 1.0\
**Projekttyp:** Community-basierte Geocaching-Routenplattform\
**Plattform:** Web Application\
**Primäre Sprache:** Deutsch (später internationalisierbar)\
**Geplante Technologie:** Laravel, PHP, MySQL/PostgreSQL, JavaScript\
**Arbeitstitel:** CacheTrail\
**Zielgruppe:** Geocacher weltweit

------------------------------------------------------------------------

# 1. Projektübersicht

## 1.1 Grundidee

CacheTrail ist eine Community-basierte Webplattform, auf der Geocacher
zusammenhängende Geocaching-Routen erstellen, veröffentlichen, entdecken
und bewerten können.

Das grundlegende Konzept besteht darin, mehrere bereits existierende
Geocaches zu einer zusammenhängenden Route zusammenzufassen.

Ein Beispiel:

Ein Geocacher hat fünf oder zehn Geocaches in einem Waldgebiet
versteckt. Ein anderer Geocacher möchte diese Caches besuchen. Anstatt
jeden Cache einzeln auf der Geocaching-Karte suchen und seine Route
selbst zusammenstellen zu müssen, kann er auf CacheTrail eine bereits
erstellte Route finden.

Diese Route enthält alle relevanten Informationen:

-   Anzahl der enthaltenen Geocaches
-   Gesamtlänge der Strecke
-   Entfernung zum Startpunkt
-   Geschätzte Dauer
-   Durchschnittliche Difficulty
-   Durchschnittliches Terrain
-   Geeignete Fortbewegungsmittel
-   Kartenansicht mit allen Stationen
-   Verlinkungen zu den einzelnen Geocaches

Die Plattform soll es außerdem ermöglichen, dass jeder Nutzer eigene
Routen aus bereits vorhandenen Geocaches zusammenstellt und diese mit
der Community teilt.

Dabei ist es nicht erforderlich, dass der Ersteller selbst Eigentümer
der enthaltenen Geocaches ist.

## 1.2 Welches Problem soll CacheTrail lösen?

Die Plattform soll mehrere Probleme des bisherigen
Geocaching-Erlebnisses lösen.

**Problem 1 -- Unübersichtliche Karten**

Die Geocaching-Karte kann insbesondere in Gebieten mit vielen Geocaches
sehr unübersichtlich werden. Es ist häufig schwierig zu erkennen, welche
Caches zusammengehören oder welche sich sinnvoll zu einer gemeinsamen
Tour kombinieren lassen.

**Problem 2 -- Fehlende Informationen zur Tour**

Wenn ein Nutzer mehrere interessante Caches gefunden hat, kann er häufig
nur schwer einschätzen:

-   Wie lange dauert die gesamte Tour?
-   Wie weit muss ich laufen?
-   Ist die Strecke für ein Fahrrad geeignet?
-   Wie viele Caches erwarten mich?
-   Wie anspruchsvoll sind die enthaltenen Caches?

**Problem 3 -- Fehlendes Routenverzeichnis**

Es soll eine zentrale Plattform geben, auf der Geocacher bereits
zusammengestellte Routen anderer Nutzer entdecken können.

Anstatt jede Tour selbst zu planen, können Nutzer auf fertige Routen
zurückgreifen.

## 1.3 Zielsetzung

CacheTrail soll keine Konkurrenz zu Geocaching.com oder bestehenden
Geocaching-Apps darstellen.

Die Plattform versteht sich als ergänzendes Community-Angebot.

Der Schwerpunkt liegt auf:

-   Entdecken von Geocaching-Routen
-   Erstellen und Veröffentlichen eigener Routen
-   Lokaler Routensuche
-   Vergleich verschiedener Routen
-   Übersichtlicher Darstellung zusammenhängender Geocaches

Die Plattform soll grundsätzlich kostenlos nutzbar sein.

Eine freiwillige Unterstützung über Buy Me a Coffee oder PayPal könnte
zu einem späteren Zeitpunkt angeboten werden.

Es sind zunächst keine kostenpflichtigen Abonnements oder
verpflichtenden Zahlungen vorgesehen.

------------------------------------------------------------------------

# 2. Zielgruppe und Benutzerrollen

CacheTrail richtet sich grundsätzlich an Geocacher weltweit.

Es gibt zwei zentrale Nutzungsszenarien. Ein Nutzer kann beide Rollen
gleichzeitig übernehmen.

## 2.1 Nutzer A -- Route Explorer

Dieser Nutzer möchte bereits vorhandene Geocaching-Routen entdecken.

Sein Ziel ist es, eine Route zu finden, die zu seinen Vorstellungen
passt.

Beispielsweise möchte er eine Runde finden, die:

-   Maximal 5 km von seinem Standort entfernt beginnt.
-   Zwischen 5 und 15 Geocaches enthält.
-   Nicht länger als 3 km ist.
-   Etwa 1--2 Stunden dauert.
-   Mit dem Fahrrad oder zu Fuß absolviert werden kann.
-   Eine durchschnittliche Difficulty von maximal 2,5 hat.

Der Nutzer soll die Ergebnisse einfach filtern und miteinander
vergleichen können.

## 2.2 Nutzer B -- Route Creator

Dieser Nutzer möchte eine eigene Geocaching-Route erstellen.

Er hat beispielsweise mehrere Geocaches entdeckt oder selbst versteckt
und möchte diese zu einer zusammenhängenden Tour verbinden.

Er soll:

-   Eine neue Route erstellen können.
-   Beliebige vorhandene Geocaches über ihre GC-Codes hinzufügen können.
-   Cache-Informationen automatisch abrufen lassen können.
-   Die Reihenfolge der Geocaches selbst festlegen können.
-   Zusätzliche Informationen zur Route eintragen können.
-   Eine Vorschau auf der Karte erhalten.
-   Die fertige Route veröffentlichen können.

Wichtig: Ein Nutzer muss nicht Eigentümer der enthaltenen Geocaches
sein.

Die Erstellung von Routen soll möglichst einfach sein.

------------------------------------------------------------------------

# 3. Authentifizierung und Berechtigungen

## 3.1 Eigener CacheTrail-Account

Jeder Nutzer benötigt zunächst einen eigenen Account auf CacheTrail.

Die Registrierung und Anmeldung erfolgt unabhängig von Geocaching.com.

Die Plattform soll ein eigenes Benutzerverwaltungssystem besitzen.

## 3.2 Verknüpfung mit Geocaching.com

Für die geplante Nutzung der offiziellen Geocaching-API soll ein Nutzer
seinen Geocaching.com-Account zusätzlich über OAuth verbinden können.

Der Ablauf:

1.  Nutzer registriert sich auf CacheTrail.
2.  Nutzer meldet sich bei CacheTrail an.
3.  Nutzer klickt auf "Connect Geocaching.com Account".
4.  Nutzer wird zu Geocaching.com weitergeleitet.
5.  Nutzer autorisiert die Verbindung.
6.  CacheTrail erhält die entsprechende OAuth-Autorisierung.
7.  Der Premium-Mitgliedschaftsstatus kann über die offizielle API
    überprüft werden, sofern dies durch die API-Freigabe gestattet ist.

Der CacheTrail-Account und die Geocaching.com-Verbindung müssen getrennt
voneinander verwaltet werden.

## 3.3 Premium-Mitgliedschaft

Das ursprüngliche Konzept sieht vor, dass bestimmte Funktionen nur
Nutzern mit einer aktiven Geocaching.com-Premium-Mitgliedschaft zur
Verfügung stehen.

Geplante Einschränkungen:

-   Routensuche und Zugriff auf das Routenverzeichnis:
    Geocaching.com-Premium-Mitgliedschaft erforderlich.
-   Erstellung und Veröffentlichung von Routen:
    Geocaching.com-Premium-Mitgliedschaft erforderlich.
-   Bearbeitung eigener Routen: Nur für den jeweiligen Ersteller.
-   Administrative Funktionen: Nur für Administratoren.

Die Premium-Verifizierung soll über die offizielle API erfolgen.

**Wichtig:** Diese Funktionalität ist noch nicht bestätigt. Die
tatsächliche Umsetzung muss von der API-Freigabe und den
Lizenzbedingungen von Geocaching HQ abhängig gemacht werden.

Das Berechtigungssystem muss deshalb so entwickelt werden, dass die
Premium-Prüfung später ausgetauscht oder angepasst werden kann.

## 3.4 Geplante Benutzerrollen

Für die erste Version:

**Guest** - Startseite ansehen - Allgemeine Informationen über
CacheTrail ansehen - Keine geschützten Routenfunktionen

**User** - Eigenen Account verwalten - Geocaching.com-Account
verbinden - Premium-Status prüfen lassen

**Premium User** - Routen entdecken - Routen filtern - Routendetails
ansehen - Routen erstellen - Eigene Routen bearbeiten und löschen -
Routen favorisieren

**Admin** - Benutzer verwalten - Routen moderieren - Unzulässige Inhalte
entfernen - Plattform verwalten

Zusätzliche Rollen wie Verified Creator können später ergänzt werden.

------------------------------------------------------------------------

# 4. Funktionale Anforderungen

## 4.1 Startseite

Die Startseite soll den Zweck der Plattform unmittelbar vermitteln.

Mögliche Inhalte:

-   Hero-Bereich mit Suchfunktion
-   Kurze Vorstellung von CacheTrail
-   Beliebte Routen
-   Neu veröffentlichte Routen
-   Routen in der Nähe
-   Erklärung, wie CacheTrail funktioniert
-   Call-to-Action zum Erstellen einer Route

Der Schwerpunkt liegt auf dem Entdecken von Routen.

## 4.2 Routenübersicht

Die Routenübersicht ist eine der wichtigsten Seiten der gesamten
Anwendung.

Hier werden veröffentlichte Routen dargestellt.

Geplante Funktionen:

-   Suchfunktion
-   Filter
-   Sortierung
-   Kartenansicht
-   Listenansicht
-   Pagination

### Filtermöglichkeiten

**Standort** - Aktueller Standort des Nutzers - Manuell eingegebener
Standort - Maximale Entfernung zum Startpunkt

**Anzahl Geocaches** - Mindestanzahl - Maximalanzahl

**Streckenlänge** - Mindestlänge - Maximallänge

**Geschätzte Dauer** - Mindestdauer - Maximaldauer

**Fortbewegungsmittel** - Zu Fuß - Fahrrad - Beides

**Difficulty** - Mindestwert - Maximalwert

**Terrain** - Mindestwert - Maximalwert

### Sortierung

-   Neueste Routen
-   Beliebteste Routen
-   Beste Bewertungen
-   Kürzeste Routen
-   Längste Routen
-   Kürzeste Entfernung zum eigenen Standort

### Beispiel eines Routenergebnisses

Name: Waldabenteuer am Niederrhein

-   Entfernung zum Nutzer: 2 km
-   Anzahl Geocaches: 10
-   Streckenlänge: 1,5 km
-   Geschätzte Dauer: 2 Stunden
-   Durchschnittliche Difficulty: 2,0 / 5
-   Durchschnittliches Terrain: 2,0 / 5
-   Fortbewegung: Zu Fuß, Fahrrad
-   Bewertung: 4,7 / 5

Die Darstellung soll übersichtlich und modern sein.

Die wichtigsten Kennzahlen sollen auf den ersten Blick erkennbar sein.

## 4.3 Routendetailseite

Jede veröffentlichte Route erhält eine eigene Detailseite.

Die Seite soll folgende Informationen enthalten:

**Allgemeine Informationen** - Routenname - Beschreibung - Titelbild -
Ersteller - Veröffentlichungsdatum - Letzte Aktualisierung

**Routeninformationen** - Anzahl der Geocaches - Gesamtlänge -
Geschätzte Dauer - Durchschnittliche Difficulty - Durchschnittliches
Terrain - Geeignete Fortbewegungsmittel - Entfernung zum Standort des
Nutzers

**Kartenansicht**

Eine interaktive Karte zeigt alle Geocaches der Route.

Jeder Cache erhält einen Marker.

Die Marker sollen entsprechend der festgelegten Reihenfolge nummeriert
sein.

Eine Verbindungslinie kann die Reihenfolge der Route visualisieren.

Beim Anklicken eines Markers sollen Informationen zum jeweiligen
Geocache erscheinen.

**Cache-Liste**

Unterhalb oder neben der Karte werden alle enthaltenen Caches angezeigt.

Beispiel:

1.  GC12345 -- Der alte Baum
2.  GC23456 -- Waldgeheimnis
3.  GC34567 -- Am kleinen See
4.  GC45678 -- Das Finale

Jeder Eintrag soll einen Link zur entsprechenden
Geocaching.com-Cache-Seite enthalten.

**Weitere Funktionen** - Route favorisieren - Route bewerten - Route
kommentieren - GPX-Export (spätere Funktion) - Route melden

## 4.4 Routenerstellung

Die Routenerstellung soll möglichst intuitiv sein.

Sie ist eine zentrale Funktion der Plattform.

Der Erstellungsprozess wird in mehrere Schritte unterteilt.

### Schritt 1 -- Allgemeine Informationen

Der Nutzer gibt folgende Informationen ein:

-   Name der Route
-   Beschreibung
-   Titelbild
-   Geschätzte Dauer
-   Fortbewegungsmittel

Die Dauer wird manuell vom Ersteller eingetragen.

Mögliche Fortbewegungsmittel:

-   Zu Fuß
-   Fahrrad
-   Zu Fuß und Fahrrad

### Schritt 2 -- Geocaches hinzufügen

Der Nutzer kann beliebig viele Geocaches anhand ihrer GC-Codes
hinzufügen.

Beispiel:

GC12345\
GC23456\
GC34567\
GC45678\
GC56789

Die Anwendung soll die Codes validieren und anschließend die jeweiligen
Cache-Informationen abrufen.

Anzuzeigende Informationen:

-   Cache-Name
-   GC-Code
-   Koordinaten
-   Cache-Typ
-   Difficulty
-   Terrain
-   Cache-Status

Der Nutzer soll die hinzugefügten Caches direkt sehen können.

Fehlerhafte oder nicht abrufbare GC-Codes müssen verständlich
gekennzeichnet werden.

### Schritt 3 -- Reihenfolge festlegen

Der Nutzer legt die Reihenfolge selbst fest.

Die Reihenfolge darf nicht automatisch verändert werden.

Die Caches sollen per Drag & Drop sortierbar sein.

Die festgelegte Reihenfolge bestimmt:

-   Die Nummerierung auf der Karte
-   Die Reihenfolge in der Cache-Liste
-   Die Berechnung der Streckenlänge

Eine automatische Routenoptimierung ist für die erste Version nicht
vorgesehen.

### Schritt 4 -- Automatische Berechnungen

Die Anwendung soll bestimmte Informationen automatisch aus den
Cache-Daten berechnen.

**Anzahl der Geocaches**

Anzahl aller gültig zugeordneten Cache-Stationen.

**Durchschnittliche Difficulty**

Arithmetischer Mittelwert aller Difficulty-Werte.

Beispiel:

2 + 2 + 3 + 1 + 2 = 10

10 / 5 = 2,0

Ergebnis: 2,0 / 5.

**Durchschnittliches Terrain**

Analog zur Difficulty.

**Gesamtlänge**

Die Strecke wird anhand der festgelegten Reihenfolge berechnet.

Wichtig: Die Luftlinie zwischen zwei Koordinaten ist nicht gleich der
tatsächlichen Wegstrecke.

Wenn möglich, soll ein Routing-Dienst verwendet werden, der vorhandene
Wege berücksichtigt.

Falls keine realistische Wegberechnung möglich ist, muss das Ergebnis
entsprechend gekennzeichnet werden.

**Geschätzte Dauer**

Wird vom Ersteller eingetragen und nicht automatisch aus der
Gehgeschwindigkeit berechnet.

Grund: Die Suche nach Geocaches benötigt zusätzliche Zeit, die sich
nicht zuverlässig aus der Streckenlänge ableiten lässt.

### Schritt 5 -- Vorschau

Vor der Veröffentlichung wird eine Vorschau angezeigt.

Der Nutzer kann kontrollieren:

-   Alle hinzugefügten Geocaches
-   Reihenfolge
-   Kartenansicht
-   Berechnete Werte
-   Allgemeine Informationen

Anschließend kann er die Route als Entwurf speichern oder
veröffentlichen.

## 4.5 Eigene Routen verwalten

Jeder Nutzer erhält einen Bereich für seine eigenen Routen.

Mögliche Funktionen:

-   Eigene Routen ansehen
-   Routen bearbeiten
-   Routen löschen
-   Entwürfe verwalten
-   Veröffentlichungsstatus ändern
-   Auf Bewertungen und Kommentare reagieren

Nur der Ersteller und Administratoren dürfen eine Route bearbeiten.

------------------------------------------------------------------------

# 5. Umgang mit Geocache-Status und Änderungen

Ein wichtiger Bestandteil der Anwendung ist die Behandlung von
Geocaches, die ihren Status verändern.

Beispielsweise kann ein Cache deaktiviert oder archiviert werden.

Die Route selbst soll dadurch nicht automatisch gelöscht werden.

## 5.1 Cache-Status

Geplante Status:

-   Active
-   Temporarily Disabled
-   Archived
-   Unknown

Die tatsächlichen Statuswerte müssen später an die offiziellen API-Werte
angepasst werden.

## 5.2 Verhalten bei archivierten Caches

Wenn ein Cache archiviert wird:

-   Die Route bleibt bestehen.
-   Der Cache bleibt in der Cache-Liste sichtbar.
-   Der Cache erhält eine deutliche Kennzeichnung.
-   Nutzer erhalten einen Hinweis, dass diese Station nicht mehr
    verfügbar ist.

Beispiel:

GC23456 -- Waldgeheimnis

Status: Archived

Diese Station ist nicht mehr verfügbar.

## 5.3 Verhalten bei deaktivierten Caches

Bei deaktivierten Caches:

-   Route bleibt bestehen.
-   Temporärer Status wird angezeigt.
-   Nutzer werden darauf hingewiesen, dass der Cache momentan nicht
    verfügbar ist.

## 5.4 Regelmäßige Aktualisierung

Die Cache-Statusinformationen sollen langfristig regelmäßig überprüft
werden.

Dafür ist ein Hintergrundprozess vorgesehen.

Die Häufigkeit und technische Umsetzung hängen von den API-Limits und
der Lizenz ab.

Es darf keine unnötige Anzahl von API-Anfragen erzeugt werden.

------------------------------------------------------------------------

# 6. Kartenintegration

Die Kartenansicht ist ein wichtiger Bestandteil von CacheTrail.

Für die erste Version ist Leaflet in Verbindung mit OpenStreetMap eine
mögliche Lösung.

Alternativ können später andere Kartenanbieter eingesetzt werden.

## 6.1 Kartenfunktionen

-   Alle Caches einer Route anzeigen
-   Nummerierte Marker
-   Verbindungslinie zwischen den Stationen
-   Klickbare Marker
-   Popups mit Cache-Informationen
-   Zentrierung auf die gesamte Route
-   Zoom-Funktion
-   Anzeige des Start- und Endpunkts

## 6.2 Routing

Für die Berechnung der tatsächlichen Wegstrecke kann später ein
Routing-Dienst wie OSRM oder openrouteservice verwendet werden.

Dabei muss geprüft werden, ob die gewünschte Fortbewegungsart
unterstützt wird.

Insbesondere sollen Fußgänger- und Fahrradrouten berücksichtigt werden.

Die verwendeten Kartendienste und deren Nutzungsbedingungen müssen
beachtet werden.

------------------------------------------------------------------------

# 7. Technische Architektur

## 7.1 Grundlegender Tech-Stack

Die Anwendung soll mit folgenden Technologien umgesetzt werden:

**Backend** - PHP - Laravel - Laravel Eloquent ORM - Laravel Queues für
Hintergrundaufgaben - Laravel Scheduler

**Datenbank** - MySQL oder PostgreSQL - Lokale Entwicklung kann zunächst
SQLite verwenden

**Frontend** - Blade oder eine passende moderne Frontend-Lösung -
JavaScript - CSS - Responsive Design

Die konkrete Frontend-Technologie soll zunächst anhand des vorhandenen
Laravel-Projekts beurteilt werden.

**Karten** - Leaflet - OpenStreetMap als mögliche Kartenquelle

**Routing** - OSRM oder openrouteservice als mögliche spätere
Integration

## 7.2 Architekturprinzip

Die Anwendung soll modular aufgebaut werden.

Besonders wichtig ist die Trennung zwischen:

-   Routendaten
-   Geocache-Daten
-   Benutzern
-   Authentifizierung
-   Externen API-Providern

Die Anwendung darf nicht direkt von einer einzigen Geocaching-API
abhängig sein.

Dafür soll eine Provider-Abstraktion verwendet werden.

## 7.3 Geocache Provider

Geplante Struktur:

GeocacheProvider (Interface)

Implementierungen:

-   GeocachingComProvider
-   OpenCachingProvider
-   ManualGeocacheProvider
-   MockGeocacheProvider

Die Anwendung kommuniziert ausschließlich über das Interface mit den
jeweiligen Providern.

Beispielhafte Methoden:

-   findByCode()
-   getDetails()
-   getStatus()

Die konkreten Methoden können an die tatsächlichen Anforderungen
angepasst werden.

**MockGeocacheProvider**

Dieser Provider ist besonders wichtig für die erste Entwicklungsphase.

Er stellt Beispieldaten bereit, ohne eine echte externe API zu
benötigen.

Dadurch kann die gesamte Anwendung entwickelt und getestet werden, bevor
eine API-Freigabe vorliegt.

------------------------------------------------------------------------

# 8. Geocaching.com API und OAuth

## 8.1 Aktueller Stand

Geocaching HQ wurde bereits kontaktiert.

Die Rückmeldung war:

-   Neue API-Partner werden nur selten aufgenommen.
-   Es gibt eine Warteliste.
-   Persönliche oder sehr kleine Projekte ohne Mehrwert für die gesamte
    Geocaching-Community werden nicht zugelassen.
-   Eine offizielle Bewerbung ist über das Partnerprogramm möglich.

Eine API-Freigabe liegt aktuell **nicht** vor.

Die Anwendung darf deshalb nicht so entwickelt werden, als wäre der
API-Zugang bereits gesichert.

## 8.2 OAuth

Bei einer späteren API-Integration sollen Nutzer ihren
Geocaching.com-Account über OAuth verbinden können.

Die eigene CacheTrail-Authentifizierung bleibt davon unabhängig.

Die OAuth-Verbindung soll folgende Aufgaben ermöglichen, sofern die API
und Lizenz dies erlauben:

-   Verbindung mit dem Geocaching.com-Account
-   Überprüfung der Premium-Mitgliedschaft
-   Abruf autorisierter Cache-Informationen

Tokens müssen sicher gespeichert werden.

Sie dürfen niemals im Frontend oder in öffentlich zugänglichen Logs
erscheinen.

## 8.3 Besonders wichtige Lizenzfrage

Das Konzept sieht vor, dass Nutzer eigene Routen mit vorhandenen
Geocaches erstellen und anschließend mit anderen Nutzern teilen.

Dabei können auch Premium-only-Caches enthalten sein.

Noch ungeklärt ist, ob Cache-Daten, die über den OAuth-Zugang eines
einzelnen Nutzers abgerufen wurden, für gemeinschaftlich veröffentlichte
Routen verwendet und anderen Nutzern angezeigt werden dürfen.

Das muss ausdrücklich von Geocaching HQ genehmigt werden.

Insbesondere darf nicht davon ausgegangen werden, dass eine individuelle
OAuth-Autorisierung automatisch eine Erlaubnis zur zentralen Aggregation
oder Weitergabe der Daten beinhaltet.

Auch die Speicherung von Cache-Daten muss mit der API-Lizenz vereinbar
sein.

## 8.4 Keine unerlaubten Datenquellen

Nicht erlaubt beziehungsweise nicht ohne vorherige rechtliche Klärung
umzusetzen sind:

-   Scraping von Geocaching.com
-   Umgehen von Zugriffsbeschränkungen
-   Nutzung nicht autorisierter APIs
-   Abruf geschützter Cache-Daten ohne entsprechende Berechtigung

Bis zur API-Freigabe wird ausschließlich mit eigenen Beispieldaten
gearbeitet.

------------------------------------------------------------------------

# 9. Vorgeschlagenes Datenbankmodell

Das folgende Datenbankmodell ist ein erster Entwurf und soll während der
Entwicklung weiter verfeinert werden.

## 9.1 users

Speichert die CacheTrail-Benutzer.

Felder:

-   id
-   name
-   email
-   password
-   role
-   created_at
-   updated_at

## 9.2 geocaching_accounts

Speichert die Verbindung zu externen Geocaching-Accounts.

Felder:

-   id
-   user_id
-   provider
-   external_user_id
-   username
-   access_token (verschlüsselt)
-   refresh_token (verschlüsselt, falls vorhanden)
-   token_expires_at
-   membership_status
-   last_verified_at
-   created_at
-   updated_at

Die konkreten OAuth-Felder müssen anhand der offiziellen
API-Spezifikation festgelegt werden.

## 9.3 routes

Speichert die allgemeinen Routendaten.

Felder:

-   id
-   user_id
-   name
-   slug
-   description
-   image
-   estimated_duration
-   transportation
-   total_distance
-   average_difficulty
-   average_terrain
-   visibility
-   published_at
-   created_at
-   updated_at

Mögliche visibility-Werte:

-   draft
-   published
-   hidden

## 9.4 route_caches

Verknüpft Routen mit Geocaches.

Felder:

-   id
-   route_id
-   provider
-   cache_code
-   position
-   cache_name
-   latitude
-   longitude
-   cache_type
-   difficulty
-   terrain
-   cache_status
-   last_checked_at
-   created_at
-   updated_at

**Wichtig:** Die dauerhafte Speicherung der extern bezogenen Cache-Daten
ist noch nicht lizenzrechtlich geklärt.

Das Datenmodell dient zunächst als technischer Entwurf. Vor der echten
API-Integration muss entschieden werden, welche Informationen
gespeichert werden dürfen und welche bei Bedarf erneut abgerufen werden
müssen.

Die Route selbst muss unabhängig von einem einzelnen Geocache bestehen
bleiben können.

## 9.5 route_favorites

Speichert favorisierte Routen.

Felder:

-   id
-   user_id
-   route_id
-   created_at

Eine Kombination aus user_id und route_id muss eindeutig sein.

## 9.6 route_ratings

Speichert Bewertungen.

Felder:

-   id
-   user_id
-   route_id
-   rating
-   comment
-   created_at
-   updated_at

Pro Nutzer und Route soll zunächst nur eine Bewertung existieren.

## 9.7 reports

Speichert gemeldete Routen oder Kommentare.

Felder:

-   id
-   user_id
-   route_id
-   reason
-   description
-   status
-   created_at
-   updated_at

------------------------------------------------------------------------

# 10. UI/UX-Anforderungen

## 10.1 Designrichtung

CacheTrail soll modern, übersichtlich und hochwertig wirken.

Die Plattform soll nicht wie eine klassische Verwaltungsoberfläche
aussehen.

Der Schwerpunkt liegt auf Karten, Routen und Entdecken.

Designprinzipien:

-   Klare visuelle Hierarchie
-   Moderne Typografie
-   Gute Lesbarkeit
-   Responsive Layouts
-   Übersichtliche Karten
-   Konsistente Komponenten
-   Wenig unnötige visuelle Elemente

Die Benutzeroberfläche soll auf Desktop, Tablet und Smartphone
funktionieren.

## 10.2 Navigation

Vorgeschlagene Navigation:

-   Home
-   Discover Routes
-   Create Route
-   My Routes
-   Favorites
-   Profile

Nicht verfügbare Funktionen sollen entsprechend der Berechtigungen
ausgeblendet oder mit einer verständlichen Erklärung versehen werden.

## 10.3 Routenkarten

Eine Route soll in der Übersicht als kompakte Karte dargestellt werden.

Inhalt:

-   Titelbild
-   Routenname
-   Ersteller
-   Anzahl Caches
-   Entfernung zum Nutzer
-   Streckenlänge
-   Geschätzte Dauer
-   Difficulty
-   Terrain
-   Fortbewegungsmittel
-   Bewertung

## 10.4 Routeneditor

Der Routeneditor soll besonders benutzerfreundlich sein.

Die wichtigsten Funktionen sollen ohne technische Vorkenntnisse
bedienbar sein.

Besonders wichtig:

-   Einfaches Hinzufügen von GC-Codes
-   Klare Fehlermeldungen
-   Drag-and-drop-Sortierung
-   Direkte Kartenaktualisierung
-   Automatische Berechnung der Kennzahlen
-   Vorschau vor Veröffentlichung

------------------------------------------------------------------------

# 11. Sicherheit und Datenschutz

Folgende Anforderungen sind zu berücksichtigen:

-   Sichere Passwortspeicherung über Laravel
-   CSRF-Schutz
-   Validierung sämtlicher Eingaben
-   Autorisierung über Laravel Policies
-   Sichere OAuth-Implementierung
-   Verschlüsselte Speicherung von Tokens
-   Keine sensiblen Daten in Logs
-   Rate Limiting
-   Schutz vor unberechtigten Änderungen an fremden Routen
-   Sichere Dateiuploads für Titelbilder

Zusätzlich muss eine Datenschutzerklärung erstellt werden.

Die Nutzung externer Karten- und Routing-Dienste muss ebenfalls
datenschutzrechtlich berücksichtigt werden.

------------------------------------------------------------------------

# 12. MVP -- Erste veröffentlichbare Version

Die erste Version soll bewusst überschaubar bleiben.

Es ist nicht notwendig, sofort sämtliche langfristig geplanten
Funktionen zu implementieren.

## 12.1 Must-have-Funktionen

-   Registrierung und Login
-   Benutzerprofile
-   Routensuche
-   Routenfilter
-   Routendetailseite
-   Interaktive Kartenansicht
-   Routenerstellung
-   Geocache-Eingabe über GC-Code
-   MockGeocacheProvider für Testdaten
-   Drag-and-drop-Sortierung
-   Automatische Difficulty-Berechnung
-   Automatische Terrain-Berechnung
-   Berechnung der Streckenlänge
-   Manuelle Eingabe der geschätzten Dauer
-   Veröffentlichung und Bearbeitung eigener Routen

## 12.2 Nice-to-have-Funktionen

Diese Funktionen können später ergänzt werden:

-   Favoriten
-   Bewertungen
-   Kommentare
-   GPX-Export
-   Routen teilen
-   Benutzer folgen
-   Routenstatistiken
-   Automatische Cache-Statusüberprüfung
-   E-Mail-Benachrichtigungen
-   Erweiterte Suchfilter
-   Mobile App

------------------------------------------------------------------------

# 13. Entwicklungsphasen

Die Entwicklung soll schrittweise erfolgen.

## Phase 1 -- Projektgrundlage

-   Bestehendes Laravel-Projekt analysieren.
-   Projektstruktur überprüfen.
-   Datenbankmodell planen.
-   Authentifizierung einrichten.
-   Grundlegendes Layout erstellen.
-   Navigation implementieren.

## Phase 2 -- Routensystem

-   Route Model erstellen.
-   Route Controller erstellen.
-   CRUD-Funktionen implementieren.
-   Routenerstellung umsetzen.
-   Routenübersicht erstellen.
-   Routendetailseite erstellen.

## Phase 3 -- Geocache-System

-   Geocache Model beziehungsweise Datenstruktur erstellen.
-   GeocacheProvider Interface implementieren.
-   MockGeocacheProvider erstellen.
-   GC-Code-Validierung umsetzen.
-   Cache-Daten abrufen.
-   Cache-Daten einer Route zuordnen.

## Phase 4 -- Karten und Berechnungen

-   Leaflet integrieren.
-   Marker darstellen.
-   Marker nummerieren.
-   Verbindungslinie darstellen.
-   Difficulty berechnen.
-   Terrain berechnen.
-   Streckenlänge berechnen.

## Phase 5 -- Community-Funktionen

-   Favoriten
-   Bewertungen
-   Kommentare
-   Benutzerprofile
-   Routenmoderation

## Phase 6 -- Externe API

Erst nach einer entsprechenden Freigabe durch Geocaching HQ:

-   OAuth integrieren.
-   Geocaching.com-Provider implementieren.
-   Premium-Verifizierung umsetzen.
-   Autorisierte Cache-Abfragen integrieren.
-   Lizenzkonforme Speicherung und Anzeige umsetzen.

------------------------------------------------------------------------

# 14. Offene Punkte und noch nicht getroffene Entscheidungen

Folgende Themen sind noch nicht abschließend geklärt und dürfen während
der Entwicklung nicht einfach als feste Anforderungen angenommen werden.

**Geocaching API**

-   Wird CacheTrail von Geocaching HQ als API-Partner zugelassen?
-   Welche Daten dürfen abgerufen werden?
-   Welche Daten dürfen dauerhaft gespeichert werden?
-   Dürfen Cache-Daten in gemeinschaftlich veröffentlichten Routen
    angezeigt werden?
-   Wie wird der Premium-Status überprüft?
-   Welche Abruflimits gelten?

**Monetarisierung**

Die Plattform soll kostenlos bleiben.

Eine freiwillige Unterstützung über Buy Me a Coffee oder PayPal ist
denkbar.

Die Zulässigkeit muss anhand der API-Lizenz geprüft werden.

**Projektname**

CacheTrail ist ein vorläufiger Projektname.

Eine endgültige Marken- und Domainprüfung steht noch aus.

**Routenberechnung**

Noch zu entscheiden:

-   Welcher Routing-Dienst wird verwendet?
-   Wie werden Fahrrad- und Fußgängerrouten berechnet?
-   Wie wird mit fehlenden oder nicht routbaren Koordinaten umgegangen?

**Premium-Zugriff**

Die ursprünglich geplante Einschränkung auf
Geocaching.com-Premium-Mitglieder muss noch mit den tatsächlichen API-
und Lizenzmöglichkeiten abgeglichen werden.

------------------------------------------------------------------------

# 15. Grundlegende Entwicklungsrichtlinien

Bei der Umsetzung von CacheTrail gelten folgende Prinzipien:

1.  Die Anwendung soll modular und wartbar aufgebaut sein.
2.  Bestehende Laravel-Konventionen sollen eingehalten werden.
3.  Keine unnötig komplizierte Architektur.
4.  Funktionen sollen möglichst wiederverwendbar implementiert werden.
5.  Geschäftslogik gehört nicht unnötig in Blade-Templates.
6.  Controller sollen möglichst schlank bleiben.
7.  Externe API-Abfragen müssen gekapselt werden.
8.  Die Anwendung muss zunächst vollständig mit Mock-Daten
    funktionieren.
9.  Die API-Integration darf keine Voraussetzung für die Entwicklung
    anderer Funktionen sein.
10. Alle wichtigen Berechtigungen müssen serverseitig geprüft werden.
11. Die Anwendung soll responsive sein.
12. Es sollen sinnvolle Tests für zentrale Geschäftslogik erstellt
    werden.

------------------------------------------------------------------------

# 16. Zusammenfassung der Produktvision

CacheTrail soll eine zentrale Anlaufstelle für Geocacher werden, die
fertige Geocaching-Routen entdecken und eigene Routen mit anderen teilen
möchten.

Der Fokus liegt auf einer einfachen Bedienung, übersichtlichen
Routendarstellungen und einer leistungsfähigen Suchfunktion.

Nutzer sollen nicht mehr selbst mühsam einzelne Geocaches auf einer
unübersichtlichen Karte zusammenstellen müssen.

Stattdessen sollen sie eine Route finden können, die ihren persönlichen
Vorstellungen entspricht.

CacheTrail soll das Geocaching-Erlebnis ergänzen und der Community
ermöglichen, besonders schöne und interessante Routen miteinander zu
teilen.

**Leitgedanke:**

Discover routes. Find adventures.

------------------------------------------------------------------------

# 17. Arbeitsauftrag für Claude

Ich möchte gemeinsam mit dir das Projekt CacheTrail entwickeln.

Lies zunächst die vollständige Datei PROJECT_SPECIFICATION.md und
verwende sie als zentrale Grundlage für die Entwicklung.

Bevor du Änderungen am Code vornimmst, analysiere bitte mein bestehendes
Laravel-Projekt und verschaffe dir einen Überblick über die vorhandene
Architektur, Framework-Version, Frontend-Technologie, Datenbankstruktur
und bereits implementierte Funktionen.

Wichtige Vorgaben:

-   Halte dich an die Anforderungen aus der Projektdokumentation.
-   Erfinde keine zusätzlichen Produktfunktionen ohne Rücksprache.
-   Berücksichtige, dass die Geocaching.com-API noch nicht freigegeben
    wurde.
-   Verwende deshalb zunächst Mock-Daten und einen MockGeocacheProvider.
-   Entwickle die Architektur so, dass später echte Geocaching-Provider
    integriert werden können.
-   Arbeite modular und nach den Laravel-Best-Practices.
-   Vermeide unnötige Abhängigkeiten und überdimensionierte Architektur.
-   Achte auf ein hochwertiges, modernes und responsives UI/UX-Design.

Beginne nicht direkt mit der vollständigen Implementierung.

Erstelle zunächst eine Analyse des bestehenden Projekts und einen
konkreten Entwicklungsplan für Phase 1.

Erkläre mir, welche Dateien und Komponenten du erstellen oder verändern
möchtest, bevor du größere Änderungen durchführst.

Wir entwickeln CacheTrail schrittweise und überprüfen nach jeder Phase
das Ergebnis.

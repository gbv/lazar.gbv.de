# lazar.gbv.de

Dieses git-Repository enthält den Quellcode der Homepage des DFG-Projekt LaZAR
unter [LaZAR](http://lazar.gbv.de/).

## Umfang

- Startseite
- Schnittstellen
    - OAI-PMH Proxy
    - Linked Open Data (RDF)

### OAI-PMH-Proxy

Der OAI-PMH-Proxy erweitert die OAI-Schnittstelle des zugrunde liegenden
Datenbanksystems easyDB um Schnittmengen von sets für Abfragen vom Typ
`ListRecords`.

## Installation

Benötigt wird mindestens PHP 7 und composer zur Paketverwaltung.

    $ composer update --no-dev

Das Einstiegsverzeichnis für den Webserver ist `public/`. Für Apache ist eine
`.htaccess` enthalten.

## Entwicklung

Benötigt PHPUnit und PHP Codesniffer:

    $ composer update

In `Makefile` sind die üblichen Aktionen zusammengefasst:

    $ make style    # PHP-Code aufräumen
    $ make test     # Unit-Tests
    $ make web      # Anwendung testweise auf Port 8008 starten


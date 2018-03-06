# lazar-oai

OAI-PMH Dokumentation und Proxy für [LaZAR](http://lazar.gbv.de/).

## Schnittmengen von Sets

Der OAI-Proxy erweitert die OAI-Schnittstelle des zugrunde liegenden
Datenbanksystems easyDB um Schnittmengen von sets für `ListRecords` Abfragen.

*Achtung:* Set-Schnittmengen tauchen nicht unter `ListSets` auf und werden bei
`ListIdentifiers` nicht unterstützt.


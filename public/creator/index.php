<?php include '../header.php'; ?>

<h1>Urheber in LaZAR</h1>
<p>
  Zur einheitlichen Erschließung von Forschungsdaten enthält das Repository
  <b>Informationen zu Urhebern</b>. Zur eindeutigen Identifizierung sind die
  Personen und Organisationen in der Regel mit 
  <a href="https://orcid.org/">ORCID</a> oder 
  mit <a href="https://www.grid.ac/">GRID</a> und mit der 
  <a href="http://www.dnb.de/DE/Standardisierung/GND/gnd_node.html">Gemeinsamen Normdatei (GND)</a>
  verknüpft.
</p>
<p>
  Eine Liste aller Orte kann
  <a href="https://lazardb.gbv.de/lists/person_urheber">in der Datenbank</a> oder
  <a href="../api/oai?verb=ListIdentifiers&metadataPrefix=easydb&set=objecttype:person_urheber">per OAI-PMH</a>
  eingesehen werden.
</p>
<h3>Beispiele (zuletzt geänderte Datensätze)</h3>
<?php
  require '../oai_list.php';
  show_oai_list("objecttype:person_urheber");
?>
<ul>
  <li><a href="../id/7786fb4d-8605-4dc5-8184-140493a5365c">Staatliche Ilia-Universität, Tiflis</a></li>
  <li><a href="../id/a5b07bf0-7186-47b8-ac9d-07ac92ea6625">Prof. Thede Karl</a></li>
</ul>
<?php include '../footer.php';

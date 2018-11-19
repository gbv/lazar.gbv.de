<?php include '../header.php'; ?>

<h1>Orte in LaZAR</h1>
<p>
  Zur einheitlichen Erschließung von Forschungsdaten enthält das Repository
  <b>Informationen zu Orten</b>. Zur eindeutigen Identifizierung sind die
  Orte in der Regel mit <a href="https://www.geonames.org/">GeoNames</a> verknüpft
  und mit geographischen Koordinaten versehen.
</p>
<p>
  Eine vollständige Liste aller Orte kann
  <a href="https://lazardb.gbv.de/lists/ort">in easyDB</a> oder
  <a href="../api/oai?verb=ListIdentifiers&metadataPrefix=easydb&set=objecttype:ort">per OAI-PMH</a>
  eingesehen werden.
</p>
<h3>Beispiele (zuletzt geänderte Datensätze)</h3>
<?php
  require '../oai_list.php';
  show_oai_list("objecttype:ort");
?>
<?php include '../footer.php';

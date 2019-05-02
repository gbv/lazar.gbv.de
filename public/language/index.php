<?php include '../header.php'; ?>

<h1>Sprachen in LaZAR</h1>
<p>
  Zur einheitlichen Erschließung von Forschungsdaten enthält das Repository
  <b>Informationen zu Sprachen</b>. Die Sprachen sind in der Regel mit 
  <a href="https://glottolog.org/">Glottolog</a> und mit ISO-Sprachcodes
  versehen.
</p>
<p>
  Eine Liste aller Sprachen kann
  <a href="https://lazardb.gbv.de/lists/sprache">in der Datenbank</a> oder
  <a href="../api/oai?verb=ListIdentifiers&metadataPrefix=easydb&set=objecttype:sprache">per OAI-PMH</a>
  eingesehen werden.
</p>
<h3>Beispiele (zuletzt geänderte Datensätze)</h3>
<?php
  require '../oai_list.php';
  show_oai_list("objecttype:sprache");
?>
<?php include '../footer.php';

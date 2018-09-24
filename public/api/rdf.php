<?php 
include '../rdf.php'; 
?>

<h2>Linked Open Data (LOD)</h2>
<p>
  Der Abruf der öffentlichen Inhalte aus LaZAR in RDF ist in Entwicklung.
</p>

<h3>Datenmodell</h3>
<p>
  LaZAR enthält Informationen über folgende Entitätstypen:
</p>
<table class="table" style="width:auto">
<thead>
  <tr>
    <th>Entität</th>
    <th colspan=2>Links</th>
  </tr>
<tbody>
<?php foreach ($entityTypes as $path => $type) { ?>
  <tr>
    <td>
      <?=$type['name']?>
    </td>
    <td>
      <a href="https://lazardb.gbv.de/lists/<?=$type['type']?>">Datenbank</a>
    </td>
    <td>
      <a href="../api/oai?verb=ListIdentifiers&metadataPrefix=easydb&set=objecttype:<?=$type['type']?>">OAI</a>
    </td>
  </tr>
<?php } ?>
</tbody>
</table>

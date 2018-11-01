<?php include '../header.php'; ?>

<h2>Linked Open Data (LOD)</h2>
<p>
  Der Abruf der öffentlichen Inhalte aus LaZAR in RDF ist 
  noch in Entwicklung.
</p>

<h3>Datenmodell</h3>
<p>
  LaZAR enthält Informationen über folgende Entitätstypen:
</p>
<table class="table" style="width:auto">
<thead>
  <tr>
    <th>Entität</th>
    <th>Datenbank</th>
    <th>OAI</th>
  </tr>
<tbody>
<?php foreach ($TYPES as $path => $type) { ?>
  <tr>
    <td>
      <a href="../<?=$path?>/"><?=$type['name']?></a>
    </td>
    <td>
      <a href="https://lazardb.gbv.de/lists/<?=$type['type']?>">Datenbank</a>
    </td>
    <td>
      <a href="../api/oai?verb=ListIdentifiers&metadataPrefix=easydb&set=objecttype:<?=$type['type']?>">LisIdentifiers</a> (easyDB),
      <a href="../api/oai?verb=ListRecords&metadataPrefix=rdfa&set=objecttype:<?=$type['type']?>">ListRecords</a> (RDFa)
    </td>
  </tr>
<?php } ?>
</tbody>
</table>

<p>
  Die einzelnen Entitäten werden durch URIs mit dem Präfix
  <code>https://lazar.gbv.de/id/</code> identifiziert.
</p>


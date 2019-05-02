<?php include '../header.php'; ?>

<h1>Objekte in LaZAR</h1>

<h3>Beispiele (zuletzt geänderte Datensätze)</h3>
<?php
  require '../oai_list.php';
  show_oai_list("objecttype:objekttyp");
?>


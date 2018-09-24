<!doctype html>
<html lang="de">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="stylesheet" href="../css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/bootstrap-lazar.css">
    <title>LaZAR-APIs</title>
  </head>
  <body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top">
      <a class="navbar-brand" href="../">LaZAR</a>
      <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarSupportedContent">
<?php
$section = basename(get_included_files()[0], '.php');
if ($section === 'index') {
  $section = basename(dirname(get_included_files()[0]));
}
?>
        <ul class="navbar-nav mr-auto">
          <li class="nav-item">
            <a class="nav-link<?= $section === 'api' ? ' active' : '' ?>"
               href="../api">APIs</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="../api/oai">OAI-PMH</a>
          </li>
          <li class="nav-item">
            <a class="nav-link<?= $section === 'lod' ? ' active' : '' ?>"
               href="../api/lod">LOD</a>
          </li>
        </ul>
      </div>
    </nav>
    <div class="container-fluid">

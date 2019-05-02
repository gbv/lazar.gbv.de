<?php

$SECTION = basename(get_included_files()[0], '.php');
if ($SECTION === 'index') {
    $SECTION = basename(dirname(get_included_files()[0]));
}

$TYPES = [
  'object' => [
      'name' => 'Objekte',
      'type' => 'objekttyp',
  ],
  'creator' => [
      'name' => 'Urheber',
      'type' => 'person_urheber'
  ],
  'location' => [
      'name' => 'Orte',
      'type' => 'ort'
  ],
  'language' => [
      'name' => 'Sprachen',
      'type' => 'sprache'
  ]
];


?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html lang="de">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="stylesheet" href="https://lazar.gbv.de/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://lazar.gbv.de/css/bootstrap-lazar.css">
    <title>LaZAR-APIs</title>
  </head>
  <body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top">
      <a class="navbar-brand" href="../">LaZAR</a>
      <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarSupportedContent">
        <ul class="navbar-nav mr-auto">
<?php foreach ($TYPES as $path => $entity) { ?>
          <li class="nav-item">
            <a class="nav-link<?= $SECTION === $path ? ' active' : '' ?>"
               href="../<?=$path?>"><?=$entity['name']?></a>
          </li>
<?php } ?>
          <li class="nav-item">
            <a class="nav-link<?= $SECTION === 'api' ? ' active' : '' ?>"
               href="../api">APIs</a>
          </li>
          <li class="nav-item">
            <a class="nav-link<?= $SECTION === 'lod' ? ' active' : '' ?>"
               href="../api/lod">LOD</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="../api/oai">OAI-PMH</a>
          </li>
        </ul>
      </div>
    </nav>
    <div class="container-fluid">

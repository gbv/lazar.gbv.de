<?php
include 'header.php';

if(isset($_GET['path'])) {
  $path = trim($_GET['path']);
  $parts = explode('/', $path);
  $parts = array_filter($parts);
  // parameter-count wrong
  if(count($parts) != 2) {
    die("Incorrect Parameters");
  }
  $uuid = $parts[0];
  $version = $parts[1];
}
else {
  die("Incorrect Parameters");
}

$xmlOrigPath = 'https://lazardb.gbv.de/api/v1/objects/uuid/' . $uuid . '/format/xml_easydb';
$xmlOrigStr = file_get_contents($xmlOrigPath);
$xmlOrig = simplexml_load_string($xmlOrigStr);
$lazardbUrl = $xmlOrig->objekttyp->_urls;
$lazardbUrl = $lazardbUrl->xpath('//*[@type="easydb-id"]');
$lazardbUrl = strval($lazardbUrl[0][0]);

echo '<h3>Dies ist Version ' . $version . ' des Datensatzes <a href="' . $lazardbUrl . '" target="_blank">' . $lazardbUrl . '</a></h3>';

// get easydb-XML for Version
$xmlVersionPath = 'https://lazardb.gbv.de/api/v1/objects/uuid/' . $uuid . '/version/' . $version . '/format/xml_easydb';
$xmlVersionStr = file_get_contents($xmlVersionPath);

$xmlVersion = simplexml_load_string($xmlVersionStr);
$xmlVersion->formatOutput = true;

$xmlString = htmlentities($xmlVersion->saveXML());

// get JSON for Version
$jsonVersionPath = 'https://lazardb.gbv.de/api/v1/objects/uuid/' . $uuid . '/version/' . $version . '/format/json';
$jsonVersionStr = file_get_contents($jsonVersionPath);
$jsonString = htmlentities($jsonVersionStr);

?>
<div class="download_formats">
  <ul class="nav nav-tabs" id="myTab" role="tablist">
    <li class="nav-item">
      <a class="nav-link active show" id="easydbXML-tab" data-toggle="tab" href="#easydbXML" role="tab" aria-controls="easydbXML" aria-selected="true">easydbXML</a>
    </li>
    <li class="nav-item">
      <a class="nav-link" id="json-tab" data-toggle="tab" href="#json" role="tab" aria-controls="json" aria-selected="false">json</a>
    </li>
  </ul>
  <div class="tab-content" id="myTabContent">
    <div class="tab-pane fade active show" id="easydbXML" role="tabpanel" aria-labelledby="easydbXML-tab">
      <p><br />Siehe <a class="external" href="<?php echo $xmlVersionPath; ?>" target="_blank"><?php echo $xmlVersionPath; ?></a>
      <p><pre><?php echo $xmlString; ?>'</pre></p>
    </div>
    <div class="tab-pane fade" id="json" role="tabpanel" aria-labelledby="json-tab">
      <p><br />Siehe <a class="external" href="<?php echo $jsonVersionPath; ?>" target="_blank"><?php echo $jsonVersionPath; ?></a>
      <p><pre><?php echo $jsonString; ?></pre></p>
    </div>
  </div>
</div>
<?php
include 'footer.php';
?>

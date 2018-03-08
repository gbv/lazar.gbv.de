<!doctype html>
<html lang="de">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="stylesheet" href="../css/bootstrap.min.css">
    <title>LaZAR-APIs</title>
  </head>
  <body>
	<nav class="navbar navbar-expand-lg navbar-light bg-light">
	  <a class="navbar-brand" href="../">LaZAR</a>
	  <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
	    <span class="navbar-toggler-icon"></span>
	  </button>
	  <div class="collapse navbar-collapse" id="navbarSupportedContent">
		<ul class="navbar-nav mr-auto">
		  <li class="nav-item active">
			<a class="nav-link" href="#">APIs</a>
		  </li>
		</ul>
	  </div>
    </nav>
	<div class="container-fluid">
		<div class="alert alert-info" role="alert">
		  Dieser Teil der <a href="../">LaZAR-Homepage</a> befindet sich noch im Aufbau!
		</div>
		<h2 id="oai">OAI-PMH</h2>
		<p>
		  Unter <code><a href="oai">oai</a></code> steht eine
		  <a href="http://www.openarchives.org/pmh/">OAI-PMH</a> Schnittstelle zum Abruf aller 
		  veröffentlichten Datensätze zur Verfügung.
		</p>
		<h3>Erweiterungen</h3>
		<p>
		  Der OAI-PMH-Endpunkt unterstützt als Nicht-Standard-Erweiterung des OAI-Protokolls 
		  Kombinationen von sets für Abfragen vom Typ <code>ListRecords</code>. Beispiel:
		</p>
		<p>
		  Beispiele:
  		  <ul>
		    <li>
		  <code><a href="oai?verb=ListRecords&metadataPrefix=easydb&set=pool:1:2,tagfilter:lza"
			>set=pool:1:2,tagfilter:lza</a></code>
			</li>
		</ul>
		</p>
	</div>
    <script src="../js/bootstrap.min.js"></script>
  </body>
</html>

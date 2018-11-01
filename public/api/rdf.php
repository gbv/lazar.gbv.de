<?php declare(strict_types=1);

function notFound()
{
    http_response_code(404);
    echo "URI not found";
    exit;
}

require '../../vendor/autoload.php';

use GuzzleHttp\Psr7\Request;

$id = substr($_SERVER['PATH_INFO'] ?? '/', 1);

if (!preg_match('/^[a-f0-9]+(-[a-f0-9]+)+$/', $id)) {
    notFound();
}

// initialize proxy
$config = json_decode(file_get_contents('../../config-oai.json'), true);
$proxy = new GBV\OAI\Proxy($config);

$record = $proxy->getRecord('rdfa', "oai:lazar.gbv.de:$id");
if (!$record) {
    notFound();
}

?>
<html>
<body>
  <?php echo $record->ownerDocument->saveXML($record); ?>
</body>
</html>

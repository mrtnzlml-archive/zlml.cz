<!DOCTYPE html>
<meta charset="utf-8">
<meta name="robots" content="noindex">

<?php

date_default_timezone_set('UTC');
$date = date('YmdHi');

$url = 'http://localhost.dev/zeminem.cz/www/api/article/10';
$hmac = hash_hmac('sha512', $url . $date, 'PRIVATE_KEY');

// create a new cURL resource
$curl = curl_init($url);

// set URL and other appropriate options
curl_setopt($curl, CURLOPT_URL, $url);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'GET');
curl_setopt($curl, CURLOPT_HTTPHEADER, array(
	'public-api-key: PUBLIC_KEY',
	"api-hash: $hmac",
));

// grab URL and pass it to the browser
$result = curl_exec($curl);

// close cURL resource, and free up system resources
curl_close($curl);

require __DIR__ . '/../vendor/tracy/tracy/src/tracy.php';
Tracy\Debugger::enable();
Tracy\Debugger::dump(json_decode($result));
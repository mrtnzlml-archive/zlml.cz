<!DOCTYPE html>
<meta charset="utf-8">
<meta name="robots" content="noindex">

<?php

require __DIR__ . '/../vendor/autoload.php';

date_default_timezone_set('UTC');
$date = date('YmdHi');

$url = 'http://localhost.dev/www.zeminem.cz/www/api/v1/articles';
$hmac = hash_hmac('sha512', $url . $date, 'PRIVATE_KEY');

$curl = curl_init($url);

$data = [
	'title' => 'Title',
	'body' => 'Body',
];

curl_setopt($curl, CURLOPT_URL, $url);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
curl_setopt($curl, CURLOPT_FOLLOWLOCATION, TRUE);
curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'GET');
//curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'POST');
//curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
curl_setopt($curl, CURLOPT_HTTPHEADER, [
	'public-api-key: PUBLIC_KEY',
	"api-hash: $hmac",
]);

$result = curl_exec($curl);
//var_dump($result);

curl_close($curl);

Tracy\Debugger::enable();
//Tracy\Debugger::dump(json_decode($result));

$texy = new Texy();
foreach (json_decode($result)->data as $article) {
	echo "<h3>$article->title</h3>";
	echo "<p>" . \Nette\Utils\Strings::truncate(strip_tags($texy->process($article->body)), 500) . "</p>";
}

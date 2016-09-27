<?php

header('HTTP/1.1 503 Service Unavailable');
header('Retry-After: 300'); // 5 minutes in seconds

?>
	<!DOCTYPE html>
	<html lang="cs" dir="ltr">
	<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<meta name="robots" content="noindex">
		<meta http-equiv="refresh" content="10">
		<title>Stránka je dočasně mimo provoz z důvodu údržby</title>
		<link rel="shortcut icon" href="favicon.ico">
		<style>
			body {
				width: 400px;
				margin: 50px auto;
			}
		</style>
	</head>

	<body>
	<script> document.body.className += ' js' </script>

	<h2>Chviličku strpení prosím&hellip;</h2>
	<p>
		Stránka je dočasně mimo provoz z důvodu údržby. Nebo jsem něco pokazil. Nebo obojí. Až bude vše hotové, tak se
		stránka <strong>sama načte</strong> a můžete pokračovat ve čtení. Než to však bude hotové,
		přečtěte si krátkou básničku.
	</p>

	<h2>Žvahlav</h2>
	<p>
		Bylo smažno,<br>
		lepě svihlí tlové se batoumali v dálnici,<br>
		chrudošní byli borolové na mamné krsy žárnící.
	</p>
	<p>
		Ó synu, střez se Žvahlava,<br>
		má zuby, drápy přeostré;<br>
		střez se i Ptáka Neklava,<br>
		zuřmící Bodostre!
	</p>
	<p>
		Svůj chopil vorpálový meč,<br>
		jímž lita soka vezme v plen,<br>
		pak used v tumtumovou seč<br>
		a čekal divišlen.
	</p>
	<p>
		A jak tu vzdeskné mysle kles,<br>
		sám Žvahlav, v očích plameny,<br>
		slét hvíždně v tulížový les<br>
		a drblal rameny.
	</p>
	<p>
		Raz dva! Raz dva! A zas a zas<br>
		vorpálný meč spěl v šmiku a let.<br>
		Žvahlava hlavu za opas<br>
		a už galumpal zpět.
	</p>
	<p>
		Tys zhubil strastna Žvahlava?<br>
		Spěš na mou hruď, tys líten rek!<br>
		Ó rastný den! Avej, ava!<br>
		Ves chortal světný skřek.
	</p>
	<p>
		Bylo smažno,<br>
		lepě svihlí tlové se batoumali v dálnici,<br>
		chrudošní byli borolové na mamné krsy žárnící.
	</p>

	</body>
	</html>

<?php

exit;

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

		<link rel="stylesheet" type="text/css" href="/webtemp/7ba031.css?1393163675">
		<link rel="shortcut icon" href="/favicon.ico">
		<script type="text/javascript" src="/webtemp/5d01af.js?1393165412"></script>
	</head>


	<body>
	<script> document.body.className += ' js' </script>

	<div class="container shadow hidden-print">
		<div class="row">
			<div class="col-lg-2 visible-lg">
				<a><div id="qr"></div></a>
			</div>
			<div class="col-lg-7 col-md-8">
				<h1><a>Martin <span style="white-space:nowrap"><span class="transf1">Zlá</span><span
								class="transf2">mal</span></span></a></h1>
			</div>
			<div class="col-lg-3 col-md-4">
				<div class="text-right" style="margin-top: 20px;">
					<a href="https://twitter.com/mrtnzlml" target="_blank">Twitter</a>
					&middot;
					<a href="https://plus.google.com/u/0/108269453578187886435?rel=author" target="_blank">G+</a>
					&middot;
					<a href="https://bitbucket.org/mrtnzlml" target="_blank">Bitbucket</a>
				</div>
			</div>
		</div>
	</div>

	<div class="container hidden-print main-nav" style="background:transparent;padding:0">
		<ul class="nav nav-pills">
			<li><a><span class="glyphicon glyphicon-home"></span> home</a></li>
			<li><a>about</a></li>
			<li><a>reference</a></li>
			<li><a>obsah <span class="glyphicon glyphicon-sort-by-alphabet"></span></a></li>
			<li><a>random <span class="glyphicon glyphicon-random"></span></a></li>
		</ul>
	</div>

	<div class="container shadow" style="padding: 20px 30px;">
		<h2>Chviličku strpení prosím...</h2>
		<p>
			Stránka je dočasně mimo provoz z důvodu údržby.
			Vzhledem k tomu, že se jedná o opravdu velký update, je nutné tuto stránku dočasně, ale úplně odstavit.
		</p>
		<h3>Co tento velký update přinese?</h3>
		<ul>
			<li>Na pozadí poběží úplně nová verze Nette Frameworku</li>
			<li>Optimalizoval jsem dotazy na dabazázi, takže vše bude rychlejší</li>
			<li>Odstranil jsem javascriptovou komponentu která vše zdržovala</li>
			<li>Přidal jsem javascriptovou komponentu, která bude lepší a rychlejší</li>
			<li>A v neposlední řadě jsem vylepšil RSS výstup</li>
		</ul>
		<p>Až bude vše hotové, tak se stránka <strong>sama načte</strong> a můžete pokračovat ve čtení&hellip; &#9786;</p>
	</div>

	<div class="container text-right hidden-print footer" style="background:transparent">
		<span xmlns:dct="http://purl.org/dc/terms/" href="http://purl.org/dc/dcmitype/Text" property="dct:title"
			  rel="dct:type"><a>Tato webová stránka</a></span>, jejímž autorem je
		<a xmlns:cc="http://creativecommons.org/ns#" property="cc:attributionName"
		   rel="cc:attributionURL">Martin Zlámal</a>, podléhá licenci<br>
		<a rel="license" href="http://creativecommons.org/licenses/by-sa/4.0/deed.cs" target="_blank">Creative Commons
			Uveďte autora-Zachovejte licenci 4.0 International </a><br>
		&copy; 2012 - <?php echo date("Y"); ?>&nbsp;<a>Martin Zlámal</a> <span
			class="glyphicon glyphicon-qrcode"></span><br>
		<a href="http://www.fresh-hosting.cz/?utm_source=zeminem&utm_medium=link&utm_campaign=fresh" target="_blank">www.fresh-hosting.cz</a>
		| <a href="https://bitbucket.org/mrtnzlml/www.zeminem.cz/issues" target="_blank">Našli jste chybu?</a>
	</div>

	</body>
	</html>


<?php

exit;

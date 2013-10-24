<?php

header('HTTP/1.1 503 Service Unavailable');
header('Retry-After: 300'); // 5 minutes in seconds

?>
<!DOCTYPE html>
<meta charset="utf-8">
<meta name=robots content=noindex>
<meta name=generator content='Nette Framework'>

<style>
	@font-face { font-family:"Audiowide"; src:url(Audiowide-Regular.ttf); }
	body { color: #333; background: white; width: 500px; margin: 100px auto }
	h1 { font-weight: bold; font-size: 65px; line-height: 1; font-family:'Audiowide', 'Open Sans', sans-serif; padding: 8px 0; }
	p { font: 21px/1.5 Georgia,serif; margin: 1.5em 0 }
	a, a:hover { color: #DA0700; text-decoration: none; }
	a:hover { text-decoration: underline; }
</style>

<title>Site is temporarily down for maintenance</title>

<h1>We're Sorry</h1>

<p>The site is temporarily down for maintenance. Please try again in a few minutes.</p>

<p><a href=".">RELOAD</a></p>

<?php

exit;

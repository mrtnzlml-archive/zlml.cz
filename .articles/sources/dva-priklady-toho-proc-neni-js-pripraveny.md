---
timestamp: 1420324989000
title: Dva příklady toho, proč není JS připravený
slug: dva-priklady-toho-proc-neni-js-pripraveny
---
Tento článek nastartoval jeden hloupý tweet. Měl jsem jej na "to do" listu již nějaký čas, ale čekal jsem až to někdo tweetne... (-:

<blockquote class="twitter-tweet" lang="en"><p><a href="https://twitter.com/MartinSadovy">@MartinSadovy</a> Možná ale to není podstatné. Podstatné je, že JS běží v prohlížeči i na serveru. Isomorphism ftw. PHP je evoluční mrtvola.</p>&mdash; Daniel Steigerwald (@steida) <a href="https://twitter.com/steida/status/551431843560824832">January 3, 2015</a></blockquote>
<script async src="//platform.twitter.com/widgets.js" charset="utf-8"></script>

Nebudu řešit jak je hloupý. Neznám totiž žádnou objektivní stupnici kam bych jej mohl zařadit. Pomůže mi však oprášit pár věcí, které mě štvou a třeba se ukáže, že to dělám úplně špatně. Poslední dobou totiž kopu za [Node.js](http://nodejs.org/) (potažmo [Express.js](http://expressjs.com/)), C i [Nette](http://nette.org/) tábor. Vše v jednom projektu. Nicméně jsem odkojen na Nette a tak některé věci řeším v jiném frameworku téměř až se slzou v oku.

Rád bych upozornil na to, že nejsem žádný superprogramátor, takže je skutečně možné, že k problému přistupuji špatně. Proto bych byl rád za rozumné podněty a připomínky, nikoliv osobní výstřednosti v komentářích...

# 1. Odkazy

A hned pěkně z ostra. Jak jsem již zmínil, jsem odkojen na Nette a proto jsem náležitě rozmazlen. Ale rozmazlen v dobrém slova smyslu. Považuji totiž framework za věc, která mi má pomoci. Nikoliv házet klacky pod nohy. Jednou z takových věcí je tvorba odkazů. Jak by to mělo být správně? Přesně tak jak je to v Nette, čili je zapotřebí najký systém, který mi umožní stavět odkazy a zároveň mi umožní je kdykoliv změnit bez zásahu do šablon. Jak je tomu v takovém Exresss.js, webovém frameworku pro Node.js? V Express.js je defaultně šablonovací systém [Jade](http://jade-lang.com/). Nemluvě o tom, jaký je to nešťastný systém, odkazy se v něm vytvářejí zhruba takto:

```html
a(href='/') Home
a(href='/about') About
```

Už asi tušíte kam mířím a proč je to podle mého soudu fatální. Takový hard-code odkazů je totiž perfektně pomalá cesta do blázince. Ve skutečnosti je však mnohem větší anekdota označování aktivních odkazů. Podívejte se na [6 WTF rad](http://stackoverflow.com/questions/10713923/node-js-jade-express-how-can-i-create-a-navigation-that-will-set-class-acti), jak tento triviální problém vyřešit. Celý princip spočívá v tom použít hard-code odkazů na více místech v šabloně:

```html
ul.nav.navbar-nav
	li(class=path == '/' ? 'active' : undefined)
		a(href='/') Home
	li(class=path == '/about' ? 'active' : undefined)
		a(href='/about') About
```

Kde se bere `path`? Ten si musíte předat v routeru, např.:

```js
router.get('/', function (req, res) {
	res.render('homepage', {
		path: '/'
	});
});
```

Tak to máme celkem 4 místa kde je to pěkně natvrďáka. Proč ne? **Proč jo?** Jistě, existuje možnost jak si to naprogramovat lépe, jinak, znova. Jen si říkám, kde se stala chyba a proč to framework nezvládá nějak lépe?

# 2. Formuláře

Jedna z věcí, která se v tomto světě řeší poněkud laxně jsou formuláře. Na to nejsem zvyklý a trošku mě to děsí. Funguje to zhruba tak, že vytvoříte v šabloně formulář:

```html
form(id='save-form', method='POST', action='/save')
	.form-group
		label(for='xxx') XXX
		input(type='text', name='xxx', class='form-control', id='xxx', placeholder='xxx', required)
	.form-group
		button.btn.btn-default Odeslat
```

A následně se napíše router pro zpracování dat:

```js
router.post('/save', function (req, res) {
	console.log(req.body.xxx); // <<< !
	res.render('save', {
		path: '/save'
	});
});
```

Jistě, je to jednoduché. Ale to prostě [není ok](http://stackoverflow.com/questions/19030220/is-it-ok-to-work-directly-on-the-data-in-req-body). Kromě toho, že je v kódu opět hard-code, tak se jedná o nádherně "ošetřený" vstup. Takový krásně čistý `$_POST`. Takto si framework nepředstauji. Možná je to proto, že je Express.js ještě moc low-level, možná také proto, že ještě neuzrál.

# Není to tak zlé

Musím však utlumit některé pobouřené čtenáře. Nebylo by totiž fér jen něco hejtovat. Bez Node.js bych danou aplikaci naprogramoval jen velmi těžko. Je totiž super, že mohu využít "event-driven, non-blocking I/O model". Toto je však věc, kterou podle mého názoru moc lidí nevyužije, protože jí prostě nepotřebuje. Kolikrát něco takového programujete? Proto když jsem měl na téma Node.js [přednášku](prednaska-na-zcu-node-js), jen velmi těžko jsem lidem vysvětloval k čemu je to vlastně dobré. A právě proto jsem použití Node.js paradoxně neukazoval na webové aplikaci. Třeba proto, že se s ním nepracuje úplně nejlépe, zároveň je však v určitých směrech nenahraditelný.

Ačkoliv jsem tedy začínal jedním hloupým tweetem, není toto rekace na něj a je mi celkem hluboce ukradený. Spíše mě zajímá jestli to co jsem zde popisoval (a celá řada dalších problémů) je normální všední den server-side JS programátora, nebo se s těmito problémy vypořádáváte jinak?

Evoluční mrtvola FTW!
---
timestamp: 1387113023000
title: Nette 2.2-dev
slug: nette-2-2-dev
---
Nedávno byla změněna vývojová verze Nette Frameworku na 2.2-dev (https://github.com/nette/nette/commit/3a426255084163ec1a2f324ea0d3e9b3139adccc).
Tato změna s sebou přinesla explozi změn. Na následujících řádcích bych rád přiblížil
některé zásadní změny, které se odehrály a je zapotřebí je upravit, aby bylo možné z verze 2.1-dev
přejít právě na verzi 2.2-dev.

# Nutné úpravy

Prvě se změnilo umístění konfigurátoru. Tato změna se samozřejmě týká souboru `bootstrap.php`.
Nově je konfigurátor v novém umístění:

```php
//$configurator = new Nette\Config\Configurator;
$configurator = new \Nette\Configurator;
```

Dále jsem si zvykl používat automatické injektování závislostí pomocí anotace `@inject`.
Pro opětovné použití je nutné zapnout `nette.container.accessors`, což ostatně napoví chybová hláška,
jelikož je tato volba v nové developměnt verzi Nette ve výchozím stavu zakázána. Config.neon:

```neon
nette:
	container:
    	accessors: TRUE
```

Nyní již bude možné anotace `@inject` používat. Další změna, které mě osobně moc nepotěšila
a nevím co jí předcházelo je zrušení podpory krátkého zápisu bloků:

```html
<!-- Předtím: -->
{#content}
	...
{/#}
<!-- Nyní: -->
{block content}
	...
{/block}
```

Tato změna se mi moc nelíbí, protože například stále funguje `{include #parent}`, což je prostě
zvláštní... Za zmínku také stojí změna třídy pro práci s databází. Zatímco se ve verzi 2.0.13
normálně používá `Nette\Database\Connection`, ve verzi 2.1-dev se přešlo na `Nette\Database\SelectionFactory`, 
nicméně ve verzi 2.1.0RC2 se již pracuje s `Nette\Database\Context` a SelectionFactory již neexistuje. 
Toto  platí i pro verzi 2.2-dev. Tato změna mi bude zřejmě dlouho trvat, než ji vstřebám.
Myslím si, že obyčejné `Nette\Database` by bylo v modelu daleko více vypovídající než nějaký Context, 
ale budiž.

Tolik k podle mého zásadním změnám, které zabrání například spuštění projektu z quickstartu. Nyní
bych rád poukázal na několik málo změn z celé té exploze, které mě zaujaly.

# Další změny

Byla odstraněna celá řada zastaralých věcí. Nemá smysl je rozebírat. Je jich hodně a zastaralé jsou
už od 2.1. Každopádně například makro `n:input` se stalo zastaralé a k dispozici je nové makro
`{inputError}`, které ošéfuje vykreslení chybové hlášky u příslušného políčka. Jééj! :-)

Lehce odlišně se také přistupuje k checkboxům a vůbec, formuláře jsou zase o něco lepší, což
předpokládám souvisí s:

<blockquote class="twitter-tweet" lang="en"><p>Chtěl jsem v rychlosti udělat příklad, jak v <a href="https://twitter.com/search?q=%23netteFw&amp;src=hash">#netteFw</a> renderovat formuláře s Twitter Bootstrapem.&#10;&#10;Zabitej den a překopaný Nette…</p>&mdash; geekovo (@geekovo) <a href="https://twitter.com/geekovo/statuses/409064701369516032">December 6, 2013</a></blockquote>
<script async src="//platform.twitter.com/widgets.js" charset="utf-8"></script>

# Konečně!

Světlo světa spatřil nový [quickstart](http://doc.nette.org/cs/2.1/quickstart) v češtině pro dnes již téměř nekatuální verzi 2.0.13.
Věřím tomu, že se jedná o daleko přínosnější věc, než psaní pokročilých návodů v angličtině
(navazujících na quickstart) a doufám, že tento počin pomůže pár lidí popostrčit dál...

Jaká změna vás zaujala nejvíce?
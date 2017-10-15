---
timestamp: 1469950790000
title: Dva šablonovací systémy zároveň
slug: dva-sablonovaci-systemy-zaroven
---
Možná pracujete na nějakém projektu, který používá jiný šablonovací systém než je Latte, ale Latte se vám natolik líbí, že ho chcete používat také. Nebo naopak používáte Latte, ale *<abbr title="Smarty? Twig? Wtf? Omg?">[doplň název šablonovacího systému]</abbr>* se vám natolik líbí, že ho chcete používat také. A nebo prostě nemáte na výběr a musíte používat více šablonovacích systémů. V takovém případě existuje asi jediné přímočaré řešení a tím je vlastní implementace `Nette\Application\UI\ITemplate`.

# TemplateFactory

Ono to vlastně zase až tak přímočaré není. Je v tom totiž malý háček. V současné době fungují Latte šablony tak, že existuje továrna `TemplateFactory`, jejíž úkolem je vytvářet `Template` objekty. Originální implementace, kterou používá většina lidí (`Nette\Bridges\ApplicationLatte\Template`) pouze deleguje renderování přímo na Latte. Nabízelo by se tedy nahradit tento objekt svým vlastním a delegovat renderování jednak na Latte a jednak třeba na Smarty. Háček je však v tom, že `Template` není služba zaregistrovaná v DIC, takže není jednoduché ji nahradit.

Proto aby bylo možné nahradit objekt `Template` vlastním, je nutné nahradit také `TemplateFactory`. Tento objekt vytváří nové instance třídy `Template` a tyto objekty dále nastavuje (přidává filtry, makra, proměnné, providery, prostě Nette specific věci). Nešvar s nahrazováním celé továrničky se už pár lidí [snažilo vyřešit](https://github.com/nette/application/issues/141), ale nikdy to nikdo nedotáhl do konce (včetně mě). Jak to tedy udělat teď?

V první řadě je třeba vytvořit si vlastní `TemplateFactory`:

```php
<?php

class TemplateFactory implements Nette\Application\UI\ITemplateFactory
{
	//...
}
```

To v podstatě znamená copy paste původní továrny. Je to trošku nepříjemné, ale je možné z toho těžit. Můžeme se například zbavit deprecated věcí, nebo si libovolně nakonfigurovat `Template` objekt. Podstatné je, že v `createTemplate` metodě vytváříme vlastní instanci `Template` objektu.

Teď přijde ta důležitá část na kterou nesmíme zapomenout. Novou vlastní `TemplateFactory` zaregistrujeme do konfiguračního souboru jako službu:

```php
services:
	latte.templateFactory: Ant\TemplateFactory
```

Tento zápis zajistí to, že se nejen `TemplateFactory` přidá do DI kontejneru, ale zároveň se nahradí původní implementace (proto to `latte.templateFactory` - důležité).

# Template

Samotný `Template` objekt už je pak prkotina. Stačí pouze změnit implementaci metody `render`. Já osobně jsem to řešil tak, že podle toho jaká přijde koncovka souboru, tak nabídnu ten správný engine pro vykreslení. Třeba nějak takto:

```php
public function render($file = NULL, array $params = [])
{
	$file = $file ?: $this->getFile();

	if (Strings::endsWith($file, '.latte') || $this->getLatte()->getLoader() instanceof \Latte\Loaders\StringLoader) {
		//tady mám něco hustého co ukážu jindy

		$this->getLatte()->render($file, $params + $this->getParameters());
   	} else { //Smarty fallback
   	    //peklo které nechce nikdy vidět

		$providers = $this->getLatte()->getProviders();
        /** @var \Nette\Application\UI\Presenter $presenter */
        $presenter = $providers['uiPresenter'];
        if ($presenter->isAjax()) {
            $this->page->fetch($file);
        } else {
            $this->page->display($file);
        }
   	}
}
```

Trošku jsem to zjednodušil aby byla podtržena myšlenka. Doporučím však ještě jednu věc a to podědit si vlastní template od `Nette\Bridges\ApplicationLatte\Template`. Chce to trošku si s tím pohrát, ale hlavní benefit bude vidět za chvíli. Zejména je dobré zaměřit se na `__set`:

```php
public function __set($name, $value)
{
	$this->assignToSmarty($name, $value); //DIY
	parent::__set($name, $value);
}
```

Teď je totiž možné používat klasické `$this->template->variable = 'xyz';` a tato proměnná bude k dispozici bez ohledu na způsob vykreslení.

# Gotchas a benefity

Každý teď tedy může používat například v komponentách `$this->template->render('***.tpl');` a zároveň mít třeba layout v Latte. Je to fuk. A to je cool. Je však třeba mít neustále na mysli, že nelze jednotlivé vykreslovací způsoby používat úplně nahodile. Styčiný bod je render metoda. Nelze tedy například používat include v Latte a vyžadovat tam šablonu ze Smarty.

Asi největší nachytávka jsou snippety. Na to jak udělat podporu snippetů do Smarty se můžeme podívat jindy - není to nic složitého. Problém byl však u kombinování jednotlivých způsobů vykreslení a předávání `snippetMode` příznaku. `snippetMode` vlastně říká, jestli se má šablona vykreslit jako snippet (tedy jen podčásti) a vrátit v payloadu. Když jsem však použil komponentu ve Smarty (vlastní `{control name=test}`) a v této komponentě normální Latte šablonu obsahující snippety, tak to prostě nemohlo fungovat. Asi nejjednoušší řešení bylo v tomto případě trošku ohnout `Template` a `snippetMode` prostě přes tu aplikaci protlačit:

```php
$presenter = $this->getLatte()->getProviders()['uiPresenter'];
if ($presenter->isAjax()) {
	//propagate snippet mode into components (Smarty templates):
	foreach ($presenter->getComponents(TRUE) as $component) {
		$component->snippetMode = $presenter->snippetMode;
	}
	$this->page->fetch($file);
} else {
	$this->page->display($file);
}
```

Není to úplně stejné jako se chová Nette k Latte, ale účel to plní dobře a to jde... :)
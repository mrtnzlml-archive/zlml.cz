No dobře, možná ne úplně definitivní, ale užitečná příručka snad. Pokusím se zde rozebrat všechny potřebné stavy generovaných továrniček, které považuji za důležité a jejich co nejjednodušší zápis v configu. Jedná se hlavně o pohled z hlediska předávání parametrů. Doufám, že to ještě někdo doplní o nějaké vylepšení, nebo další příklad, abych mohl tento seznam rozšířit. To je jeden ze dvou důvodů tohoto typu článků. Ten druhý je, abych měl kam odkazovat, až se mě někdo bude opět ptát.

Celkem rozebírám tyto jednotlivé případy:
- [Předání parametru z presenteru](#toc-predani-parametru-z-presenteru)
- [Předání parametru z konfiguračního souboru](#toc-predani-parametru-z-konfiguracniho-souboru)
  - Metodou "create"
  - Metodou "arguments"
- [All in One](#toc-all-in-one)

Předání parametru z presenteru
==============================
Toto považuji za asi úplně nejčastější požadavek. Komponenta je jednoduchá:

```php
<?php

class ParameterComponent extends Nette\Application\UI\Control {
	public function __construct(array $xxx) {}
}

interface IParameterComponentFactory {

	/** @return ParameterComponent */
	function create(array $xxx);

}
```

Důležité je, že jak datový typ, tak název proměnné se musí shodovat. Config pak není o nic složitější:

```neon
services:
	- IParameterComponentFactory
```

Kontejner se potom vygeneruje dle očekávání:

```php
final class Container_59ca411ae5_IParameterComponentFactoryImpl_28_IParameterComponentFactory implements IParameterComponentFactory {

	private $container;

	public function __construct(Container_59ca411ae5 $container) {
		$this->container = $container;
	}

	public function create(array $xxx) {
		$service = new ParameterComponent($xxx);
		return $service;
	}

}
```

Samotné použití je velmi jednoduché. Stačí si nechat v presenteru předat interface `IParameterComponentFactory` například pomocí anotace `@inject` a nad ním volat metodu `create`. Fígl je právě v tom, že vygenerovaný kód v kontejneru tento interface implementuje a odvádí tak zbytečnou práci za vás. Bez dalších změn lze využít autowire zaregistrovaných služeb. Předání parametru z configu a zároveň získání další závislosti pak může vypadat třeba takto (pouze upravená předchozí komponenta):

```php
<?php

class ParameterComponent extends Nette\Application\UI\Control {
	public function __construct(array $xxx, App\Model\UserManager $userManager) {}
}
```

Předání parametru z konfiguračního souboru
==========================================
Toto je trošku horší, ale pořád snadno pochopitelné. Kód komponenty bude opět podobný:

```php
<?php

class ConfigComponent extends Nette\Application\UI\Control {
	public function __construct($configParam) {}
}

interface IConfigComponentFactory {
	function create();
}
```

Všimněte si, že je v tomto případě úplně zbytečná `@return` anotace. Co má factory vytvářet lze totiž specifikovat v configu:

```neon
parameters:
	testkey1: testvalue1

services:
	- implement: IConfigComponentFactory
	  create: ConfigComponent(%testkey1%)
```

Zde by skoro šlo přestat interface úplně psát. To ale není v současné době možné a vygenerovaný kód je pak přesně takový, jaký by měl být:

```php
final class Container_59ca411ae5_IConfigComponentFactoryImpl_33 implements IConfigComponentFactory {

	private $container;

	public function __construct(Container_59ca411ae5 $container) {
		$this->container = $container;
	}

	public function create() {
		$service = new ConfigComponent('testvalue1');
		return $service;
	}

}
```

Alternativně lze zvolit populárnější způsob a upravit konfigurační soubor takto:

```neon
parameters:
	testkey1: testvalue1

services:
	- implement: IConfigComponentFactory
	  arguments: [%testkey1%]
```

Vygenerovaný výsledek je stejný. V tomto případě je však nutné dát pozor na to, že při psaní interface je nutné psát jej i s `@return` anotací.

Ok, toto je snad jasné. Co to trošku zkomplikovat?

All in One
==========
Toto snad bude dostatečně krajní případ. Pokusíme se vytvořit továrničku pro komponentu, která bude ke svému vytvoření vyžadovat parametr z configu, parametr z presenteru, službu a opět parametr z configu - vše přesně v tomto pořadí. A nebudu se v tom snažit hledat závislosti. Je vyžadováno něco takového:

```php
<?php

class AllInComponent extends Nette\Application\UI\Control {

	public function __construct($configParam1, array $userParam, App\Model\UserManager $userManager, $configParam2) {}

}

interface IAllInComponentFactory {

	/** @return AllInComponent */
	function create(array $userParam);

}
```

Je tedy jasné, že musím vytvořit `create` metodu s parametrem, který naplním v presenteru. Zde by opět `@return` anotace nemusela být. Je úplně zbytečná. A jak na ty parametry z configu? To už je přece vyřešené viz dřívější ukázky:

```neon
parameters:
	testkey1: testvalue1
	testkey2: testvalue2

services:
	- implement: IAllInComponentFactory
	  create: AllInComponent(configParam2: %testkey2%, configParam1: %testkey1%)
```

Zde jsem si to ještě zkomplikoval tím, že jsem zadal parametry v obráceném pořadí (což by přesně takto fungovalo). Abych docílil správného pořadí, musím parametry správně pojmenovat (shodně s konstruktorem komponenty). A vygenerovaný kód? Radost pohledět:

```php
final class Container_59ca411ae5_IAllInComponentFactoryImpl_32 implements IAllInComponentFactory {

	private $container;

	public function __construct(Container_59ca411ae5 $container) {
		$this->container = $container;
	}

	public function create(array $userParam) {
		$service = new AllInComponent('testvalue1', $userParam, $this->container->getService('27_App_Model_UserManager'), 'testvalue2');
		return $service;
	}

}
```

I v tomto případě je možné zvolit jiný (úspornější) zápis v konfiguračním souboru. Vygenerovaný výstup je opět stejný:

```neon
parameters:
	testkey1: testvalue1
	testkey2: testvalue2

services:
	- implement: IAllInComponentFactory
	  arguments: [configParam2: %testkey2%, configParam1: %testkey1%]
```

Teď mě nenapadá, jestli je někdy (běžně) potřeba ještě něco složitějšího. Toto jsou však dva základní přístupy zkombinované do jedné ukázky. Podívejte se na všechny tyto [příklady podrobněji na GitHubu](https://github.com/mrtnzlml/generated-factories).
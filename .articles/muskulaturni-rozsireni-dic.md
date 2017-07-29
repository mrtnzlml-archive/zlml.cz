K čemu je DI rozšíření v Nette a jak se takové rozšíření píše [už víme](http://zlml.cz/rozsireni-pro-dic). Teď se podíváme na způsob, jak pracovat s takovým rozšířením na úplně nové úrovni. Tento článek velké spoustě lidí změní způsob práce a aplikace budou najednou o level výš. Jak řekl jeden z účastníků školení: tak to je geniální... :)

Jak strukturovat aplikaci?
==========================
O tom už jsem se párkrát rozepsal a ještě se také minimálně jednou rozepíšu. Proteď jen rychlý úvod. Za tu dobu co dělám s frameworky jsem došel k tomu, že nemám žádnou složku s názvem `app`. Například struktura [tohoto projektu](https://github.com/adeira/connector) vypadá (zjednodušeně) takto:

```
.
├── bin
│   └── console
├── config
│   ├── config.local.neon
│   ├── config.local.neon.travis
│   ├── config.neon
│   ├── extensions.neon
│   └── services.neon
├── log
├── src
│   ├── Authentication
│   ├── Common
│   ├── Devices
│   ├── Endpoints
│   └── Routing
├── temp
├── tests (obsahuje 'src' se stejnou strukturou)
├── vendor
└── www
```

Na tom není vůbec nic převratného. Důležitý je však způsob jakým se pracuje se službami (services). Pokud se totiž podíváme na obsah souboru `services.neon`, tak zjistíme, že je téměř prázdný:

```neon
services:
	migrations.codeStyle: Adeira\Connector\Migrations\CodeStyle
	router: Adeira\Connector\Routing\RouterFactory::createRouter

	doctrineSession: Adeira\Connector\Common\Infrastructure\Application\Service\DoctrineSession
	dummySession:
		class: Adeira\Connector\Common\Infrastructure\Application\Service\DummySession
		autowired: no
```

Přitom celý projekt má v tuto dobu zhruba 80 služeb, které je potřeba zaregistrovat. Kde je tedy ta magie? Asi je zřejmé kam mířím. O vše se starají rozšíření dependency injection kontejneru. Ty jsou zaregistrovány v `extensions.neon`:

```neon
extensions:
	- Arachne\ContainerAdapter\DI\ContainerAdapterExtension # because of migrations
	- Arachne\EventDispatcher\DI\EventDispatcherExtension # because of migrations
	authentication: Adeira\Connector\Authentication\Infrastructure\DI\Nette\Extension
	devices: Adeira\Connector\Devices\Infrastructure\DI\Nette\Extension
	doctrine.orm: Adeira\Connector\Doctrine\ORM\DI\Extension(%debugMode%)
	fakeSession: Kdyby\FakeSession\DI\FakeSessionExtension
	graphql: Adeira\Connector\GraphQL\Bridge\Nette\DI\Extension
	migrations: Zenify\DoctrineMigrations\DI\MigrationsExtension
	symfony.console: Adeira\Connector\Symfony\Console\DI\Extension
```

Jak je vidět, tak každý balíček ve složce `src` má vlastní rozšíření (můž mít klidně víc rozšíření, ale není to potřeba). Na následujících řádcích ukážu jak takové rozšíření napsat super jednoduše.

Rozšíření bez znalosti Nette\DI
===============================
Psaní rozšíření pro DIC v Nette může být (a je) poměrně složité. Trošku to chce vědět, jak Nette funguje uvnitř. To samozřejmě dává do rukou obrovský nástroj, ale současně to také klade obrovskou překážku. Přesně z tohoto důvodu vznikl balíček [adeira/compiler-extension](https://github.com/adeira/compiler-extension), který jsem napsal pro lidi ve firmách, kteří se Nette teprve učí, chtějí psát aplikace tak jako já a na prozkoumávání Nette\DI není čas. Záběr tohoto balíčku není jen zde. Sám jsem si ho moc oblíbil a dnes tak píšu rozšíření také (ne vždy, ale dost často).

Myšlenka je taková, že NEON formát umí každý. Pokud ne, tak si stačí prohlédnout [tuto stránku](https://ne-on.org/) a je to všem jasné (používám velmi úspěšně na školeních a přednáškách). Zároveň je snadné naučit o čem je DI, proč se musí v Nette registrovat služby v konfiguračním souboru a jak funguje autowiring. To v zásadě stačí k tomu, aby člověk začal psát aplikace mnohem lépe než dříve. Jenže pokud chce někdo strukturovat aplikaci tak jak to dělám já, tak musí registrovat všechny služby do souboru `services.neon` a těch je desítky až stovky (ne-li tisíce). Navíc je to nesmysl - proč by si takový balíček nemohl nést všechno s sebou (včetně konfigurací)?

Ale on může! Podívejte se, jak vypadá takové rozšíření `Authentication` balíčku:

```php
<?php declare(strict_types = 1);

namespace Adeira\Connector\Authentication\Infrastructure\DI\Nette;

use Adeira\Connector\Doctrine\ORM;

class Extension extends \Adeira\CompilerExtension implements ORM\DI\IMappingFilesPathsProvider
{

	public function provideConfig(): string
	{
		return __DIR__ . '/config.neon';
	}

	public function getMappingFilesPaths(): array
	{
		return [__DIR__ . '/../../Persistence/Doctrine/Mapping'];
	}

}
```

Důležitá je metoda `provideConfig`, která slouží pouze k tomu, aby rozšíření prozradilo, kde je jeho konfigurační soubor. A tato konfigurace může být [pěkně bohatá](https://raw.githubusercontent.com/adeira/connector/03be1b949a0eb0c2f75c90ba3da5fca2ef8b2979/src/Authentication/Infrastructure/DI/Nette/config.neon). Takovou nutnou prerekvizitou k tomu aby vše fungovalo je nahrazení výchozího `ExtensionsExtension` za novou implementaci, který toto chování umoňuje:

```php
$configurator = new Nette\Configurator;
$configurator->defaultExtensions['extensions'] = \Adeira\ConfigurableExtensionsExtension::class;
```

Tuto jednu řádku je nutné umístit třeba do souboru `bootstrap.php` kde se vytváří DI kontejner. Od teď bude toto chování fungovat "by default" a vlastní DI rozšíření dokonce může dědit od `Nette\DI\CompilerExtension`. **Není tedy potřeba dělat žádné úpravy ve stávajících rozšířeních.** A to je vždy super! Pokud bude rozšíření dědit od `Adeira\CompilerExtension`, budete mít k dispozici ještě pomocnou metodu `setMapping`, která se hodí pro mapování presenterů. Není to však nutná podmínka.

To ale není všechno!

Jak se chovají konfigurace balíčků
==================================
Asi nejzajímavější na návrhu dependency injection je to, že je možné jednoduše vyměňovat implementace bez zásahu do kódu. Jak se tímto pracuje balíček [adeira/compiler-extension](https://github.com/adeira/compiler-extension)? Představte si, že máte hlavní konfigurační soubor s tímto obsahem:

```neon
parameters:
    key1: value1
    key2: value2

services:
    - DefaultService
    named: Tests\Service

extensions:
    ext2: CustomExtension2

ext2:
    ext_key1: ext_value1
    ext_key2: ext_value2

application:
    mapping:
        *: *
```

A teď přidáte nový balíček, který si nese vlastní konfigurační soubor a pomocí metody `provideConfig` jej dává k dispozici. Jeho obsah je takovýto:

```neon
parameters:
    key2: overridden
    key3: value3

services:
    - Tests\TestService
    named: Service2

ext2:
    ext_key2: overridden
    ext_key3: ext_value3

latte:
    macros:
        - App\Grid\Latte\Macros
```

Jaký je výsledek? V aplikaci budou k dispozici najednou tři parametry (obdobně pro `ext2` parametry):

```neon
parameters:
    key1: value1
    key2: overridden
    key3: value3
```

Podobně se to chová i u služeb:

```neon
services:
    - DefaultService
    named: Service2 # přepsat lze pouze pojmenovanou službu
    - Tests\TestService
```

Navíc se zaregistruje Latte makro. Ačkoliv toto chování funguje dobře, doporučuji jej spíše nevyužívat k přepisování globální konfigurace. Mnohem vhodnější je využívat tyto konfigurace k **přidávání** funkčností z balíčků. Tedy registrace nových služeb, přidávání commandů do konzole, registrace nových typů v Doctrine a podobně. V takovém případě se bude rozšíření chovat naprosto očekávaně. Vyhnete se tak tomu, že dva balíčky nastavují jeden parametr a záleží tam na pořadí. Je to nástroj - užijte jej s rozumem.

To ale pořád není všechno!

Malé pozlátko na závěr
======================
Tento balíček přidává ještě jednu funkci, kterou považuji také za velmi užitečnou. Jak jistě víte, tak rozšíření se dá zaregistrovat pomocí sekce `extensions` a pokud rozšíření zaregistrujete pod nějakým jménem, je možné jej konfigurovat. To ostatně bylo vidět před malou chvílí:

```neon
extensions:
    ext2: CustomExtension2

ext2:
    ext_key1: ext_value1
    ext_key2: ext_value2
```

V tomto případě budou klíče `ext_key1` a `ext_key2` k dispozici v samotném rozšíření. To se potom používá k různým úpravám chování samotného balíčku. Co když však nepíšete vlastní PHP kód, ale chcete jen předat tyto parametry do nějaké služby, kterou ono rozšíření registruje? K tomu slouží zvláštní zápis pomocí `%%`. V tomto konkrétním případě řekněme, že `CustomExtension2` má vlastní konfigurační soubor s tímto obsahem:

```
services:
    - Tests\TestService(%%ext_key2%%)
```

Jak je vidět, tak si může vzít hodnotu `ext_key2` rovnou z konfigurace. Důležité je si uvědomit, že zatímco `%aaa%` bere parametr `aaa` ze sekce `parameters`, tak `%%aaa%%` bere konfiguraci **pouze** ze sekce, pod kterou je rozšíření zaregistrované. Chová se to tedy úplně stejně jako `$this->getConfig()` uvnitř rozšíření... :)

Dejte [tomuto rozšíření](https://github.com/adeira/compiler-extension) šanci (nebo hvězdičku). Z praxe mohu říci, že se s ním pracuje skutečně dobře a pokud narazíte na to, že potřebujete udělat něco složitého - není problém pokračovat v psaní DI rozšíření v PHP zároveň s tímto. Uvítám také nápady na zlepšení a různé postřehy. Přecijen chvíli mi trvalo, než jsem přišel na ten správný způsob jak to uchopit.

```
composer require adeira/compiler-extension
```

Instalace je jednoduchá... :)
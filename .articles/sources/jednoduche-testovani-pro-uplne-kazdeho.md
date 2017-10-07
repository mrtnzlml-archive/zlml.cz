---
id: 9b774295-d6af-413e-9431-6a05bdedfd7e
timestamp: 1454786953000
title: Jednoduché testování pro úplně každého
slug: jednoduche-testovani-pro-uplne-kazdeho
---
Konec slibů, článek je tu. Tentokrát se rozepíšu o nástroji [Testbench](https://github.com/mrtnzlml/testbench). Testbench by měl pomoci s rychlým testováním Nette aplikace. Je zaměřen spíše integračně a vhodně doplňuje [Nette\Tester](https://tester.nette.org/), který je zaměřen spíše jednotkově. Myšlenka, která stála za vytvořením tohoto nástroje je velmi prostá - testování je složité. Je složité hlavně pro lidi, kteří dokonale nerozumí problému. Proto je tento nástroj zaměřen na rychlý start pro úplně každého (kdo si prošel alespoň quickstart a chce testovat). To se projevuje v tom, jak je Testbench postaven (viz další povídání). Testbench se sestává z různých nápadů, které jsem všude možně okoukal za posledních X měsíců a něco mě na nich zaujalo. Pojďme se tedy společně podívat jak se Testbench používá a proč ho sám rád používám pro rychlé testy.

# Základní nastavení testovacího prostředí

U každého testování je vhodné testovat v co nejvíce čistém prostředí. Proto je dobrý nápad vytvořit si vlastní bootstrap (`tests/bootstrap.php`), jehož obsah může být velmi jednoduchý:

```php
<?php

require __DIR__ . '/../vendor/autoload.php';

Testbench\Bootstrap::setup(__DIR__ . '/_temp');
```

Jediným parametrem se nastaví odkládací složka pro cache testů a testy jsou připraveny. Prakticky jsou však potřeba další dodatečné konfigurace. Zde pomůže druhý parametr `setup` metody:

```php
Testbench\Bootstrap::setup(__DIR__ . '/_temp', function (Nette\Configurator $configurator) {
	$configurator->createRobotLoader()->addDirectory([
        __DIR__ . '/../app',
    ])->register();

    $configurator->addParameters([
        'appDir' => __DIR__ . '/../app',
        'testsDir' => __DIR__,
    ]);

    $configurator->addConfig(__DIR__ . '/../app/config/config.neon');
    $configurator->addConfig(__DIR__ . '/tests.neon');
});
```

Proč takto zvláštně přes callback? Zvykem je totiž, že bootstrap vrací rovnou instanci DIC. To se však hodí pouze pro aplikaci, nikoliv pro testy. Testbench si oproti tomu tuto konfiguraci uschová a když bude někdy v testech potřeba DIC, tak si jej pomocí této konfiguraci vytvoří. Tento kontejner navíc vytváří pouze jednou, aby se ušetřil čas při dalším testování.

Díky tomu, že se Testbench stará o DI kontejner sám, může si dovolit dělat zajímavé věci. Jednou z nich je například skutečnost, že se sám registruje jako rozšíření do DIC, takže je možné v konfiguračních NEON souborech používat sekci `testbench`. Ta se v současné době hodí pouze pro práci s databází:

```neon
testbench:
	dbname: <nazev_databaze>
	sqls:
		- %testsDir%/_helpers/sqls/1.sql
        - %testsDir%/_helpers/sqls/2.sql
```

Přesně tak. Když přijde na přetřes práce s databází (zatím jen Doctrine), Testbench si vytvoří úplně čistou databázi (kterou na konci testu smaže) a postupně do ní nahraje zde vyjmenované SQL soubory (např. pro základní strukturu + nějaká demo data). Název databáze je zde potřeba z toho důvodu, že se při mazání potřebuje na nějakou databázi připojit a tu dočasnou (testovací databázi) smazat. Zatím nevím, jak to udělat lépe.

Toto je asi tak vše, co je potřeba udělat před prvním spuštěním. K dispozici je potom spustitelný skript ve vendoru `vendor/bin/run-tests`, který funguje na Win i na Linuxu a pokud je vše připraveno podle předchozího návodu, tak po spuštění promaže cache testů, nastaví správně Nette\Tester a spustí jej. Zde asi časem budu dělat ještě hodně úprav, ale chci je dělat postupně - jak co bude potřeba.

# Testujeme presentery

U testování presenterů to vlastně celé začalo. Napsat si takový základní test na presenter je velmi jednoduché. Stačí použít tu správnou traitu a je půl práce hotovo:

```php
<?php //HomepagePresenterTest.phpt

require __DIR__ . '/../bootstrap.php';

/**
 * @testCase
 */
class HomepagePresenterTest extends \Tester\TestCase
{

	use \Testbench\TPresenter;

	public function testRenderDefault()
	{
		$this->checkAction('Homepage:default');
	}

	public function testRenderDefaultModule()
    {
        $this->checkAction('Module:Homepage:default');
    }

}

(new HomepagePresenterTest())->run();
```

Z této ukázky plyne jedna důležitá věc. Testbench podporuje pouze PHP 5.4 a více (5.5, 5.6, 7.0 a HHVM). Co se zde vlastně testuje? Testbench si přebere první parametr, spustí danou akci na daném presenteru a provede úplně nejzákladnější testy které lze provést. To je konkrétně kontrola správné odpovědi a dále se pokusí najít základní HTML prvky na stránce. Je to velmi jednoduchý test, ale o tom to celé je. Už samotné spuštění akce presenteru může odhalit nějaké hloupé chyby v aplikaci. Že je to málo? Metoda `checkAction` vrací [IResponse](https://api.nette.org/2.3.8/Nette.Application.IResponse.html), takže je možné dopsat si vlastní testy podle potřeby. V tomto duchu se to celé nese - otestovat jen to základní a nudné a předat otěže programátorovi, ať si své speciální případy otestuje sám.

Takových základních a nudných věcí ja samozřejmě více (viz [readme](https://github.com/mrtnzlml/testbench/blob/master/readme.md)). Může se hodit například testování přesměrování `checkRedirect`, signálů `checkSignal`, JSON odpovědí `checkJson` nebo testování RSS a sitemap (`checkRss` a `checkSitemap`). Zajímavé může být také testování formulářů:

```php
public function testSearchForm()
{
    $response = $this->checkForm('<action-name>', '<form-name>', [
        'input' => 'value',
    ]);

    //Tester\Assert::... with IResponse $response
}
```

Opět se Testbench postará o potřebné nudné náležitosti, otestuje to základní a vrátí zpět odpověď se kterou je možné cokoliv dalšího je potřeba. Tesbench "by default" testuje, jestli došlo po odeslání formuláře k přesměrování. To je asi nejčastnější chování u formulářů. Neprovedení přesměrování je tedy považováno za chybu. Toto lze ovlivnit čtvrtým parametrem. Ten může být `FALSE` - kontrola na přesměrování se neprovádí, nebo může být přímo nějaká URL cesta. Pak se kontroluje, jestli formulář přesměroval na správnou URL.

K dispozici je ještě AJAX varianta `checkAjaxForm`, která testuje formulář v AJAX módu aplikace a jako výsledek očekává [JsonResponse](https://api.nette.org/2.3.8/Nette.Application.Responses.JsonResponse.html). Čtvrtým parametrem lze pak předat cestu jako v předchozím případě. V této situaci se nejdříve provede test formuláře s normálním přesměrováním a následně ještě ten samý formulář v AJAX módu.

V neposlední řadě je možné se v testech přihlašova a odhlašovat podle libosti pomocí metod `logIn` a `logOut`.

# Testování komponent

Tato traita je novější, takže toho neumí tolik (nikdo asi nic dalšího zatím nepotřeboval). V praxi se ale ukázalo jako šikovné ověřit si, že naše skvělá, malá a chytrá komponenta vykresluje to co má. k tomu slouží `checkRenderOutput`:

```php
use \Testbench\TComponent;
public function testComponentRender()
{
    $control = new \Component;
    $this->checkRenderOutput($control, '<strong>OK%A%'); //match string
    $this->checkRenderOutput($control, __DIR__ . '/Component.expected'); //match file content
}
```

Interně zde Testbench využívá [match](https://tester.nette.org/#toc-assert-match) resp. `matchFile` z Nette\Testeru. Je to šikovná pomůcka jak si rychle ověřit, že třeba komponenta co se stará o `<title>` se o něj stará skutečně správně a vrací takové HTML, jaké vracet má. Navíc se vnitřně komponenta připojuje k `PresenterMock`, který má zkrácený životní cyklus oproti běžným presenterům - takže by to celé mělo být hned rychlejší. Pokud by připravený mock z nějakého důvodu nevyhovoval, je možné jej vyměnit:

```php
services:
	testbench.presenterMock: CustomPresenterMock
```

Takových mocků je připravená celá řada, mrkněte se do [kódu](https://github.com/mrtnzlml/testbench/tree/master/src/mocks). Může se hodit...

# Práce s databází

Když začnou mít testy velké ambice a potřebují pracovat s databází, je zde jednoduché řešení ve formě `Testbench\TDoctrine` traity. Jak název napovídá, tak Testbench momentálně podporuje pouze Doctrine, protože s ničím jiným momentálně nepracuju. Ačkoliv je příprava práce s databází asi nejsložitější, tak samotná traita poskytuje pouze jednu metodu na získání EntityManageru:

```php
use \Testbench\TDoctrine;
public function testDatabase()
{
    $em = $this->getEntityManager();
    //Tester\Assert::...
}
```

Testbench tedy připravuje čisté izolované databáze, konfiguruje jednotlivé testy a dává k dispozici připravený ObjectManager resp. EntityManager z Kdyby. Teď už se může programátor jakkoliv nad testovací databází vyřádit. Klidně bych přidal i další funkce, ale jak jsem již psal. Nechce se mi přidávat hovadiny. A proč pouze Doctrine? Protože jsem další databáze ještě nenapsal. Ani vlastně nevím jak to udělat správně vzhledem k tomu, že se používají traity. Asi by bylo nejpohodlnější napsat další traitu, třeba `TNetteDatabase` nebo `TDibi`. Stejně tak existuje [tato issue](https://github.com/mrtnzlml/testbench/issues/7) která narážela na skutečnost, že se může traita při MySQL chovat jinak než PostgreSQL. Ani to vlastně nevím jak udělat správně. Takže když nekdo budete mít volnou chvilku, tak ocením jakoukoliv pomoc... :)

# Drawbacks

Nemám rád traity. Jsou sice cool, ale nemám je rád. Hlavně asi kvůli tomuto [bugu v PHP](https://bugs.php.net/bug.php?id=63911). Ale myslím si, že zrovna Testbench je vhodné místo, kde lze traity použít lépe, než cokoliv jiného. Jen je třeba myslet, že může v určitých situacích nastat problém. Zároveň také zatím není stabilní tag této druhé verze, takže zatím používejte `dev-master` (případně existuje RC). Stabilní mám v plánu vydat někdy po tomto článku až sesbírám ohlasy a zapracuju je. Zároveň bych rád také vyřešil již zmiňovanou issue.

# Advantages

Testbench sám o sobě obsahuje poměrně bohaté testy. Byla by ostuda, kdyby to tak nebylo. Jak jsem se zde snažil popsat, tak napsat nějaké rychlé testy, které odhalí největší chyby je velmi jednoduché. Sám Testbench rád používám. Zejména teď je tato knihovna mojí velkou oporou, protože pracuji s legacy kódem, který je velmi složité otestovat. Jakýkoliv test tedy může zachránit můj zadek a proto se mi i ty nejzákladnější testy na presentery (a hlavně na `UI\Control` komponenty) hodně hodí. Využití ale bude mít i u jednoduchých webů, kde není skoro co testovat, protože aplikace skoro nic nedělá, ale je fajn vědět, jestli ještě všechny stránky fungují. U složitějších webů by měl Testbench poskytnout dostatečnou oporu při konfiguraci prostředí s tím, že v ničem nebrání a je možné dopsat si vlastní testy.

Jsem zvědav, kam co budu ještě přidávat za funkce. Asi to bude hodně kopírovat stav té legacy appky. Ale rád bych to dotáhl až někam k akceptačním testům, pokud to nebude zbytečně složité. To je ale daleká budoucnost.

[Have fun!](https://github.com/mrtnzlml/testbench)
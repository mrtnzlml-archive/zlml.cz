---
id: 314fff27-204c-449e-9975-572db54475a2
timestamp: 1375611559000
title: Routování v Nette - prakticky
slug: routovani-v-nette-prakticky
---
<div class="alert alert-success">Tento článek byl naposledy revidován, aktualizován a rozšířen <strong>27. června 2014</strong>...</div>

V následujícím článku se budu opírat o teorii napsanou v [dokumentaci](http://doc.nette.org/cs/routing).
Jelikož jsem se však Nette učil sám, tak vím jak je těžké routování pochopit
a zvlášť potom z dokumentace, která spíše ukazuje fičury, než jak na to. A vzhledem k tomu, že mi
pod rukama prošlo velké množství velmi různorodých aplikací, kád bych zde uvedl příklady
adresářové struktury, rout pro daný praktický problém a vzniklé URL adresy.
Začíná přehlídka několika možných rout. Myslím si, že celá řada příkladů bude užitečnější, než teorie.

Základ všeho je porozumět tomu, jak se v Nette vytváří [odkazy](http://doc.nette.org/cs/presenters#toc-vytvareni-odkazu).
Od toho se velmi podobně sestavují obecné routy tak, aby alespoň jedna seděla svým tvarem na daný odkaz.

# První kroky

Začněme jednoduchou statickou stránkou, která má tuto jednoduchou adresářovou strukturu:

```
app/
├─── config/
├─── model/
├─── presenters/
│     └── HomepagePresenter.php
│
├─── router/
├─── templates/
│     ├── Homepage/
│     │    ├── kontakt.latte   (zde jednotlivé stránky statického webu)
│     │    └── ...
│     └── @layout.latte
│
└─── bootstrap.php
```

To znamená, že ne každou stránku se budu odkazovat přibližně jako <code>Homepage:kontakt</code>. Samozřejmě 
vždy se záměnou šablony (v tomto případě kontakt - kontakt.latte). To je dost triviální a stačilo by například:

```php
$router[] = new Route('<presenter>/<action>[/<id>]', 'Homepage:default');
```

To je sice funkční, bohužel je to spíše teoretická routa, protože výsledek je otřesný:

```
http://zlml.cz/homepage/kontakt
```

Tuto routu píšu téměř všude. Je to routa velmi obecná a říká přibližně následující:
Bude-li se někdo odkazovat v obecném tvaru `Presenter:view`, pochop `Presenter` jako název presenteru (např. **Homepage**Presenter) a hledej tedy soubor `HomepagePresenter.php`
a `view` bude šablona presenteru, hledej ji tedy ve složce `Presenter/view.latte` a sestav URL která bude přesně v tomto tvaru.
Homepage:default pouze říká co je výchozí hodnota a co se má hledat, pokud nebude specifikována konkrétní šablona.

Pro takto malý web je mnohem lepší specifikovat konkrétnější routu, která přijde **před** onu obecnou:

```php
$router[] = new Route('<action>', 'Homepage:default');
```

Což udělá téměř to samé, jen vypustí z URL nadbytečnou informaci o presenteru. Vždy používáme HomepagePresenter, jen
se mění cílová šablona podle URL:

```
http://zlml.cz/kontakt
```

Výsledná sada rout pro takovouto malou statickou stránku by tedy mohla vypadat takto:

```php
public function createRouter() {
	$router = new RouteList();
	$router[] = new Route('<action>', 'Homepage:default');
	$router[] = new Route('<presenter>/<action>[/<id>]', 'Homepage:default');
	return $router;
}
```

Dále je vhodné používat např. soubor <code>sitemap.xml</code>. Pokud ho také umístím do stejného adresáře jako šablony, routa je opět jednoduchá:

```php
$router[] = new Route('sitemap.xml', 'Homepage:sitemap');
```

Hledá se šablona <code>sitemap.latte</code>. Přečtěte si jak vytvořit tuto šablonu v článku [RSS a Sitemap jednoduše a rychle](rss-a-sitemap-jednoduse-a-rychle). Výsledná URL je tak jak má být:

```
http://zlml.cz/sitemap.xml
```

To samé lze udělal pro RSS.

# Jdeme do hloubky

Trošku složitější routování přichází vždy když chcete udělat něco speciálního.
Například to, aby číslo za URL udávalo číslo stránky v paginatoru:

```
http://zlml.cz/2
```

```php
$router[] = new Route("[<paginator-page [1](2)>]", array(
	'presenter' => 'Homepage',
	'action' => 'default',
	'paginator-page' => 1
));
```

Zde už je nutné druhý parametr rozepsat a více specifikovat. Toto akceptuje pouze konkrétní čísla.
a jako druhou specialitu lze napsat takovou routu, která bude tvořit URL z názvů článků:

```
http://zlml.cz/using-fulltext-searching-with-innodb
```

```php
$router[] = new Route('<id>', array(
	'presenter' => 'Single',
	'action' => 'article',
	'id' => array(
		Route::FILTER_IN => function ($url) {
			return $this->posts->getIdByUrl($url);
		},
		Route::FILTER_OUT => function ($id) {
			return $this->posts->getUrlById($id);
		},
	),
));
```

A není úplně na škodu vytvořit routu, která bude řešit napríklad vyhledávání:

```
http://zlml.cz/search/fio%20api
```

```php
$router[] = new Route('search[/<search>]', 'Search:default');
```

Toto jsou jednoduché routy pro jednoduchou adresářovou strukturu. Lehce složitější jsou pro
modulární strukturu, kdy je zapotřebí specifikovat modul:

```
http://zlml.cz/rss.xml
```

```php
$router[] = new Route('rss.xml', 'Front:Blog:rss');
```

Chová se to stejně jako u předchozího příkladu se `sitemap.xml`, v tomto příkladu však routa hledá `BlogPresenter.php` ve složce `FrontModule` a šablonu `rss.latte`, také v tomto modulu. U rout pro modulární aplikace již raději rozepisuji druhý parametr, protože je to přehlednější. Následující routa zvládne jazykové mutace pro FrontModul, jinak je to opět ta nejobecnější routa vůbec:

```
http://zlml.cz/en/site/kontakt
```

```php
$router[] = new Route('[<lang cs|sk|en>/]<presenter>/<action>[/<id>]', array(
	'module' => 'Front',
	'presenter' => 'Homepage',
	'action' => 'default',
));
```

To samé, ale opět o trošku náročnější. Tentokrát pro UserModule, který je na jiné URL, než FrontModule:

```
http://zlml.cz/user/en/setting/password
```

```php
$router[] = new Route('user/[<lang cs|sk|en>/]<presenter>/<action>[/<id [0-9]+>]', array(
	'module' => 'User',
	'presenter' => 'Board',
	'action' => 'default',
));
```

A na závěr ještě poslední přehled možných rout jako příklady toho co je možné.

```php
$router[] = new Route('sitemap.xml', 'Front:Export:sitemap');
$router[] = new Route('kategorie/<category>', 'Front:Product:default');
$router[] = new Route('produkt/<product>', 'Front:Product:detail');
$router[] = new Route('', 'Front:Product:default');
$router[] = new Route('admin/sign-<action>', 'Admin:Sign:');
$router[] = new Route('registrace/', 'Front:Register:new');
$router[] = new Route('index.php', 'Homepage:default', Route::ONE_WAY);
$router[] = new CliRouter(array('action' => 'Cli:Cli:cron'));
$router[] = new \App\RestRouter('api[/<presenter>[/<id>]]', array( //vyžaduje speciální objekt (není součástí Nette)
	'module' => 'Rest',
	'presenter' => 'Resource',
	'action' => 'get',
), \App\RestRouter::RESTFUL);
```

Je zcela zřejmé, že se všechny konstrukce stále opakují, proto považuji za opravdu důležité
perfektně pochopit tvorbu odkazů a následně je to možná trochu o experimentování, ale s
touto sadou příkladů bude myslím jednoduché najít podobnou routu, jaká je zrovna potřeba.

Jak na v posledním příkladu zmíněný CLI router se dočtete v článku [Nette 2.1-dev CliRouter](nette-2-1-dev-clirouter).

Máte nějakou zajímavou routu? Podělte se o ni... (-:
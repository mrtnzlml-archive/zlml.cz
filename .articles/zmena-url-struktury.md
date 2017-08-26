---
id: dbf85219-5173-4c7b-924a-636e86172b93
timestamp: 1382905620000
title: Změna URL struktury
slug: zmena-url-struktury
---
Rád bych tímto upozornil na změny URL adres na tomto webu. A zároveň k technické povaze tohoto webu
prozradím i bližší informace ze zákulisí.

Před úpravou URL struktury jsem definoval několik důležitých bodů, kterých jsem se držel:
1. Musí zůstat maximální (úplná) zpětná kompatibilita
2. Výsledné URL musí být maximálně jednoduché a cool

# Předtím a potom

Důležité je, aby stará URL adresa nekončila chybou 404, ale aby přesměrovala na novou URL.
RSS je nyní na adrese http://zlml.cz/rss. Původně bylo na adrese http://zlml.cz/homepage/rss.
Obě dvě adresy fungují stále, rozdíl je v tom, že homepage/rss nyní přesměrovává pomocí 301 na */rss.
Toho se dalo docílit velice jednoduše:

```php
$router[] = new Route('rss', 'Homepage:rss'); //nová routa
$router[] = new Route('<presenter>/<action>[/<id>]', 'Homepage:default'); //původní
//záleží na pořadí!
```

Ačkoliv je teď druhá routa zbytečná, nechal jsem jí pro případ, že bych měl nějakou URL nezachycenou.
V tom případě ji zachytí tato obecná.

# Zpětná kompatibilita

Stejně tak jako změna adresy RSS, tak si i ostatní adresy musí zachovat stejné vlastnosti viz první bod.
A vzhledem k tomu, že jsem se rozhodl změnit adresu vyhledávání a tagů, nezbývalo, než další
dvě routy přidat. Opět záleží na pořadí:

```php
$router[] = new Route('s[/<search>]', 'Search:default'); //nová
$router[] = new Route('t[/<search>]', 'Tag:default'); //nová

//tyto routy (až uznám za vhodné) mohu smazat:
$router[] = new Route('search[/<search>]', 'Search:default', Route::ONE_WAY); //původní
$router[] = new Route('tag[/<search>]', 'Tag:default', Route::ONE_WAY); //původní
```

Opět platí, že funguje jak stará adresa http://zlml.cz/search/nette s přesměrováním 301, tak i nová 
http://zlml.cz/s/nette. Obdobně je tomu u tagů.

# Čarodějnictví!

Největší sranda však začíná u druhého požadavku. Už dlouho jsem si pohrával s myšlenkou,
že chci názvy článků a adresy jednotlivých stránek v menu hned za lomítkem jako v kořenovém
adresáři. Tedy aby článek měl adresu http://zlml.cz/lovec-matematik a stránka http://zlml.cz/about.
Zde jsem se však vždy dostával do velkého problému. Jak rozlišit a nabídnout z databáze článek a 
jak poznat, kdy naopak nabídnout stránku například s referencemi?

No, tak nejdříve je zapotřebí routa pro články:
```php
$router[] = new Route('<slug>', 'Single:article');
```
Kdy v presenteru tahám články z databáze podle slugu:
```php
public function renderArticle($slug) {
	$post = $this->posts->getBySlug($slug)->fetch(); //načetní článku podle slugu
	if (!$post) { //článek neexistuje (db vrací FALSE)
		$this->forward($slug); //nabídni statickou šablonu
	} else { // zobrazení článku
    	//...
    }
}
```
Což je podle mého dostatečně elegantní řešení. Jednoduše se pokusím o načtení stránky podle slugu
z databáze a když se to nepodaří, nabídnu nějaký latte soubor, pokud existuje. Pokud neexistuje, 
tak ErrorPresenter již obstará vrácení 404, což je správně, protože není co nabídnout...

Ještě je zapotřebí vytvořit jednu routu:
```php
$router[] = new Route('<action>', 'Single:article');
```
Bez této routy by to také fungovalo, ale latte ony latte soubory (action) bych našel na dvou URL
adresách, což nechci. Takže se z původního http://zlml.cz/single/about dostanu na http://zlml.cz/about, 
což je cool a splňuji tak druhý požadavek.

Poslední routa, která stojí za zmíňku pak zajišťuje stránkování. Pouze rozpoznává čísla podle
regulárního výrazu a podle toho stránkuje:
```php
$regex = '1|2|3'; //zjednodušeně
$router[] = new Route("[<paginator-page [$regex]>]", array(
	'presenter' => 'Homepage',
	'action' => 'default',
	'paginator-page' => 1
));
```

Ostatně to jak mám v době psaní tohoto článku router vytvořený můžete zjistit na [bitbucketu](https://bitbucket.org/mrtnzlml/zlml.cz/src/0580e2e9f0e4edb162fe97ad563cfef766bea625/app/router/RouterFactory.php).

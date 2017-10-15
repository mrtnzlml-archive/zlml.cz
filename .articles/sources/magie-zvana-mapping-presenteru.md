---
timestamp: 1459632882000
title: Magie zvaná mapping presenterů
slug: magie-zvana-mapping-presenteru
---
Ona to vlastně ani není taková magie jako to není nikde pořádně popsané. Než se pustím to obludných složitostí, bylo by vhodné zmínit se co to vlastně mapping presenterů je. Viděli jste někde toto v konfiguraci?

```neon
application:
	mapping:
		*: App\*Module\Presenters\*Presenter
```

Určitě ano, je to totiž vykopírované ze sandboxu. Tato konfigurace říká, kde má Nette hledat presentery. Resp. pod jakým **namespace**. To je důležité. Na adresářové struktuře totiž v tomto případě vůbec nezáleží. Kdyby v konfiguračním souboru nebyl mapping vůbec uvedený, presenter by musel být bez namespace, tedy například `\HomepagePresenter`. Pokud by pak zase někdo měl raději MVC, mohl by si jednoduše změnit mapping:

```neon
application:
	mapping:
		*: App\*Module\Controllers\*Controller
```

A používat tak třídu `\App\Controllers\HomepageController`. Opět na umístění v adresářové struktuře nezávisle. Pojďme se ale ponořit hlouběji a rozeberme si co vlastně jednotlivé části mappingu znamenají.

# Do hlubin regulárů

Nejdůležitější metodou je v tomto případě `\Nette\Application\PresenterFactory::setMapping`. V této metodě se ukrývá tento regulární výraz, který kontroluje validitu mappingu (preg_match):

```
#^\\\\?([\w\\\\]*\\\\)?(\w*\*\w*?\\\\)?([\w\\\\]*\*\w*)\z#
```

V této obludnosti se skrývají 3 části. Ale zjednodušeně řečeno rozdělí tento regulární výraz mapping na prefix, část s modulem a část s presenterem. Tedy například takto:

```
                                           App\
App\*Module\Presenters\*Presenter    =>    *Module\
                                           Presenters\*Presenter
```

Z toho jak je regulár napsaný by mělo být zřejmé, že lze první i druhou část vynechat. Všechny následující mappingy jsou tedy validní:

```
*Module\Presenters\*Presenter
App\Presenters\*Presenter
Presenters\*Presenter
*Presenter
Presenter*
*
```

Tak moment. Co vlastně znamenají ty hvězdičky? Zjednoušeně hvězdička značí proměnný název presenteru (Homepage v HomepagePresenter) a pokud jsou v mappingu dvě hvězdičky, tak první značí proměnnný název modulu (nebo všech zanořených modulů) aplikace. Nejpochopitelnější bude vyzkoušet si tu nejjedodušší formu mappingu:

```neon
application:
	mapping:
		*: *
```

Na hvězdičku před dvojtečkou zatím nehleďme. Nette teď bude hledat prostě jen třídu `\Homepage` (resp. podle definice routeru). Pokud budeme mít modulární aplikaci, tak se bude hledat `<Admin>Module\Homepage` (opět záleží na routeru). Pokud trošku pozměníme mapping, musí to už být úplně jasné:

```neon
application:
	mapping:
		*: *\*
```

Nette teď nebude nic řešit. Řekněme že máme router nastavený tak, aby destination bylo `Front:Homepage:default`. V při tomto mappingu se bude hledat přesenter s názvem `\Front\Homepage`. Ještě uvedu jeden příklad, aby to bylo úplně zřejmé. Teď nebudu mít destination v routě jen `Homepage:default` a mapping nastavím takto:

```neon
application:
	mapping:
		*: App\Presenters\*Presenter
```

Přesně tak, bude se to chovat úplně stejně jako první příklad. Když totiž aplikace nepracuje s moduly, tak se druhá část toho velkého reguláru zahazuje. Než se dostanu k limitním případům, kdy je mapping omezující, rozeberme si ještě konfigurační část před dvojtečkou (klíč pole). Do této chvíli všude byla jen hvězdička. To znamená, že se tento mapping aplikuje na všechny příchozí požadavky. Se vzrůstající složitostí projektu však může přijít požadavek, že chceme mapovat jinak administraci a jinak zbytek aplikace. Routování by mohlo být třeba takové:

```php
public static function createRouter()
{
	$router = new RouteList;
	$router[] = new Route('test', 'Admin:Homepage:default');
	$router[] = new Route('<presenter>/<action>[/<id>]', 'Homepage:default');
	return $router;
}
```

A mapping:

```neon
application:
	mapping:
		*: App\*Module\Presenters\*Presenter
        Admin: App\Controllers\*Controller
```

Takže vše bude mapováno pomocí první masky, ale pro `Admin` modul bude používat MVC jako v druhé ukázce. Tohoto se dá velmi dobře využít pokud stavíte aplikaci pomocí [rozšíření pro DIC](rozsireni-pro-dic). V tomto případě je totiž docela dobrý nápad mít presentery ve vlastní namespace a tedy vytvořit si pro ně nový mapping, který bude mapovat jen tuto část aplikace. Používám to hodně.

# Limitující případy

Co vím, tak existují dva limitující případy s tím, že ten druhý je vyřešen v budoucí verzi Nette Application (zatím jen dev-master). Na první případ narážím velmi často. Bohužel totiž není možné (pokud vím) definovat více mappingů pro jeden modul. Nejde tak vytvořit mapping třeba pro modul API a zároveň mapping, který by využíval stejný modul, ale byl umístěn v úplně jiném namespace (bundle). To je při tvorbě bundles hodně limitující. Tento problém je vyřešen ve skvělé knihovně [librette/presenter-factory](https://github.com/librette/presenter-factory). Celkově se v Librette skrývá spoustu pokladů, jen <del>Kdyby</del> je autor nějak dokumentoval... ;-)

Druhý případ je dobře popsán v [tomto issue](https://github.com/nette/application/issues/101). Totiž jak jsem se letmo zmínil dříve, tak pokud existuje více zanořených modulů, tak se maska pro moduly opakuje. Ale ne tak, jak by bylo občas potřeba. Mějme například tento mapping z issue #101:

```neon
application:
	mapping:
		*: App\Module\*Module\Presenter\*Presenter
```

Pokud budeme mít v routeru nadefinováno více zanoření modulů (`A:B:Homepage:default`), tak se bude hledat tento presenter:

```
App\Module\AModule\BModule\Presenter\HomepagePresenter
```

To docela dává smysl, že? Jenže ne vždy je to vhodné chování a lepší by bylo, aby se pro moduly opakovala větší část definice a hledaný presenter se ve skutečnosti jmenoval takto:

```
App\Module\AModule\Module\BModule\Presenter\HomepagePresenter
```

Toho však nelze se současným mappingem jednoduše dosáhnout. Řešením je právě vývojová verze balíčku Nette\Application, kde lze mapping nadefinovat také pomocí pole:

```neon
application:
	mapping:
		*: [App, Module\*Module, Presenter\*Presenter]
```

Vyřešení takové mappingu je už jednoduché a aplikace se chová přesně podle očekávání. Původní chování samozřejmě zůstává stejné. Při tomto novém zápisu opět platí vše co jsem již psal, jen je třeba dodržet pořadí v poli:

```neon
application:
	mapping:
		*: ['', *, *]    #nebo *\*
```

Tedy opět je hledaný presenter `Front\Homepage` při routeru nastaveném na `Front:Homepage:default`. A poslední příklad uvedu přepsaný první příklad podle tohoto alternativního přístupu:

```neon
application:
	mapping:
		*: [App, *Module, Presenters\*Presenter]
```

Víc o tom asi nejde napsat, protože je to přesně takto jednoduché... :)
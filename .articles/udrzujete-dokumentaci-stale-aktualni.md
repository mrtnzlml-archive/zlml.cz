Již dlouho si v hlavě pohrávám s jednou myšlenkou, kterou stále nemohu dovést do zdárného konce. Již na samém začátku jsem již však věděl, že se zajisté nezalíbí velké skupině programátorů. Přesto si myslím, že má něco do sebe. Jen jsem ji ještě nedomyslel tak, aby jsem s ním byl spokojen. Třeba bude mít někdo nějaký geniální nápad.

# Na začátku byl problém

A každý problém by se měl řešit. Mluvím teď o jednom konkrétním. *Jak udržet dokumentaci projektu aktuální?* To je problém, který některé projekty dokáží bez větších problémů. Obdivuji člověka, který napíše kus kódu a k němu napíše přehlednou a užitečnou dokumentaci. Ještě více však obdivuji toho, kdo aktualizuje kus kódu a opět se pustí na přepisování dokumentace. V praxi je toto však bolístka, která trápí většinu projektů, které používá menší než obrovské množství...

Myslím si, že tento postup je částečně zcestný. Není problém napsat dokumentaci, ale problém je se pak vracet k napsaným textům a číst je znova a znova a stále je upravovat. Má tento problém vůbec nějaké řešení? Možná ano. Pokud zůstanu u myšlenky, že jednou napsat dokumentace a dost, může se leckomu zdát, že při tomto postupu není možné dokumentaci aktualizovat. Definjme tedy alespoň rámcově tyto pojmy. Pod dokumentací si představuji webovou stránku s případnou obsahovou strukturou, která obsahuje jak veškeré naučné texty, tak ukázky kódů. Praktické ukázky. Tak jak to ve skutečnosti funguje.

Běžné zadání dokumentace, že? Nikde jsem však nenapsal, že tato dokumentace musí obsahovat přímo napsané povídání. Musí tento text ve výsledku obsahovat, ale nemusí být součástí!

# Co prosím?

Myslím to přesně tak jak jsem napsal. Dokumentace musí na výstupu obsahovat veškeré texty a prostě všechno, ale nemusí je při tvorbě obsahovat. To zní možná trošku divně. Není ta věta v rozporu sama se sebou? Ne nutně. Dokumentaci bych si opravdu představoval jako soubor pravidel obsahující nadpis, několik programových direktiv a to by bylo v podstatě všechno. Mohlo by to vypadat například takto pro nějaký tutoriál:

```
Toto je nadpis stránky v dokumentaci

index.php
bootstrap.php
HomepagePresenter.php:renderDefault
```

Schválně jsem zvolil všem tolik známý sandbox z Nette Frameworku. V souboru bych tedy jen definoval jen (omáčku okolo), nadpis, soubory odkud se má dokumentace generovat, popř. nějaký výběr. Zde je nutné říct, že by byl projekt po programové stránce poněkud zvláštní a nejsem si jist, jestli je to úplně OK. usel by totiž obsahovat onu dokumentaci viz např. *index.php*:

```php
<?php

/**
 ** Zde je umístněna dokumentace.
 ** Obsahuje kompletní poučný text, který se pak vyfiltruje do dokumentace včetně
 ** řádků, popř. metod ke kterám se vztahuje. Pro lepší použití by bylo potřeba
 ** definovat několik zřejmě anotací jako např:
 **
 ** @doc-lines 12-14
 ** @doc-highlight 14
 **/
$container = require __DIR__ . '/../app/bootstrap.php'; ///>label

$container->application->run();
```

Takovýto soubor je pak jednoduché vzít, rozebrat, naservírovat text, aplikovat funkci entit a vykreslit i kód ke kterému se tento komentář vztahuje. Možná by šlo vytvořit i nějaká návěští pro odkazování se do kódu, protože číslo řádky není úplně nejvhodnější (<code>///>label</code>).

# Pro et Contra

Jednoznačně by tento postup vedl k tomu, aby programátor kromě psaní kódu udržoval i komentář, který by byl běžně velmi blízko. Jednalo by se tak vlastně o jednu práci. Netřeba otevírat celou dokumentaci, stačí změnit pouze malou část, která se s pushnutím zobrazí i v dokumentaci. Na druhou stranu, nedovedu si tento postup představit v kombinaci s klasickým PHPDOC. Nenapadá mě jiné řešení, než udržovat klasický projekt a vedle projekt, který by sloužil pouze pro dokumentaci. Například onen sandbox z NetteFW. Při takovém postupu mi to však dává docela dobrý smysl. Dokumentace by byla doslova stejně aktuální jako zdrojové kódy a to včetně ukázek! Co je u takových projektů důležitější?

Mimochodem. PHP k tomuto má velmi blízko. Minimálně podle jejich dokumentačních "slohokomentářů". S tímto problémem také lehce souvisí verzování projektů o kterém bych se chtěl rozepsat jindy.

Teď však zpět k myšlence. Jak moc je to hloupý nápad? Co je jeho překážkou? Proč by ho nešlo prakticky použít?
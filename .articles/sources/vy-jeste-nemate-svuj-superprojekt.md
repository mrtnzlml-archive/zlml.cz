---
timestamp: 1484849186000
title: Vy ještě nemáte svůj superprojekt?!
slug: vy-jeste-nemate-svuj-superprojekt
---
Nenechte se ošálit. Superprojekt je skutečně [oficiální název](https://git-scm.com/docs/git-submodule#git-submodule-add) pro Git projekty, které virtuálně obsahují další podprojekty (tzv. submoduly). Jedná se o skvělý způsob jak vytvořit jeden velký repozitář a spravovat v něm mnoho knihoven. Následující text proto bude zajímavý pro programátory, kteří vydávají knihovny podobně jako to dělám já s [projektem Adeira](https://github.com/adeira).

Začněme však krátkou motivací. Tomuto způsobu vývoje resp. tomu jednomu konkrétnímu repozitáři se velmi často říká "monolith repository". Vývoj v jednom repozitáři by měl minimalizovat režii, která je nutná pro obskakování mnoha repozitářů. Důležité však je uvědomit si hned na začátku, že jeden monolitický repozitář rozhodně neznamená jeden monolitický kód. Pořád se bavíme o vydávání mnoha Composer balíčků (například), ale jejich údržba a vývoj probíhá v jednom okně jednoho IDE. Pouze finální produkt je množství dílčích repozitářů!

Výhody jsou následující:
- změny v API napříč celou knihovnou je možné dělat rovnou (nikoliv postupně balíček co balíček)
- veškeré změny Git repozitáře lze vyřešit jedním šmahem (commit, push) a není tedy potřeba dělat to ručně postupně
- tím, že se vše dělá na jednom místě, tak by vývoj měl být pružnější a jistější
- perfektně se vyřeší vlastní závislosti - říkám dej mi `moje-knihovna`, nikoliv `moje-knihovna:v2.5.3`
- celkově se celá monorepo obluda chová podobně jako jeden balíček, ale lze jej vydávat jako mnoho Composer balíčků
- lze otestovat veškerý namespace (třeba `nette/*`) najednou

Chvíli jsem to zkoumal a existují dvě cesty jak takový repozitář vytvořit. Jedna horší a jedna lepší. Začnu tou (z mého pohledu) horší...

# Split repozitáře

Toto bylo to první po čem jsem šel. Teorie zní následovně. Veškerý kód mám v jednom repozitáři (třeba `adeira/monorepo`) a normálně pracuji na kódu jak jsem zvyklý. Následně spustím nějaký program, který mi repozitář rozdělí pomocí Gitu na jednotlivé dílčí repozitáře. To lze udělat pomocí [subtree](https://github.com/git/git/blob/master/contrib/subtree/git-subtree.txt) nebo třeba pomocí [splitsh](https://github.com/splitsh/lite) (to používá Symfony).

Výhody jsou zřejmé - dělám na jednom kódu a mám jeden repozitář. To je ostatně to jak jsem monolitický repozitář vydefinoval. Nevýhody už možná tak zřejmé nejsou - mám jen jeden repozitář. Pokud jste alespoň trošičku políbeni Gitem, tak si umíte asi představit, co to znamená pro vydávání nových verzí jednotlivých balíčků. V Gitu totiž nelze mít dva stejné tagy. A to znamená dvě věci: budu vydávat verze nějak strašně složitě, nebo budou mít všechny balíčky stejnou verzi.

Druhým zmíněným způsobem to dělá Symfony a s veškerou otevřeností si myslím, že to není dobře. To totiž znamená, že budu vydávat verze ve kterých se **vůbec nic nezměnilo**. Nevěříte? Symfony to dělá běžně ([2.8.11...2.8.14](https://github.com/symfony/security-http/compare/v2.8.11...v2.8.14), [v2.8.5...v2.8.15](https://github.com/symfony/ldap/compare/v2.8.5...v2.8.15)). Když se nic nezměnilo, tak nemá smysl vydávat verzi (nebo?). Takto SemVer verzování nefunguje... Ale uvědomuji si, že jsou lidé, kteří toto budou obhajovat do morku kosti a že se jedná o můj subjektivní názor. Mnohem více mě trápí to, jak si subtree hraje s historií. Ačkoliv dokumentace tvrdí, že subtree vždy vrátí pro stejnou historii stejné výsledné SHA, tak z pokusů můžu potvrdit, že to není pravda (nezkoumal jsem dál proč). A to je hodně špatně - na všech repozitářích mám naštěstí zabezpečenou master větev, abych nemohl udělat force push. Lépe k tomu přistupuje Splitsh, který je navíc násobně rychlejší. Ten však zase neumí pracovat s historií, která byla pomocí subtree přidána. Možná znáte subsplit - to je ale jen obálka nad subtree (takže stejně pomalé).

Pokud tedy chcete vydávat balíčky kdy se bude často stávat, že má jeden commit několik tagů verzí a nemáte problém s tím, že je nutné pokaždé dopočítat diff z historie pro dílčí projekty, pak je pro vás split zřejmě ta správná volba. V opačném případě je tu varianta se superprojektem.

# Superprojekt

Superprojekt na to jde úplně jinak. Využívá tzv. [submoduly](https://git-scm.com/docs/git-submodule) což není nic jiného než ukazatel na jiný **plnohodnotný** Git repozitář. Při této konfiguraci se superprojekt chová jako přepravka na tyto repozitáře. Přesně ví kde jsou a jak s nimi pracovat, ale nehraje si s jejich historií. Vzhledem k tomu, že je v submodulu plnohodnotný Git repozitář, tak mohu pracovat s tagy samostatně a vydávat tak verze samostatně. Není tedy třeba dělat žádné ústupky.

Superprojekt si drží informaci o submodulech v souboru `.gitmodules` jehož obsah vypadá takto (příklad z projektu [adeira/superproject](https://github.com/adeira/superproject)):

```js
[submodule "Component/compiler-extension"]
	path = Component/compiler-extension
	url = git@github.com:adeira/compiler-extension.git
[submodule "Component/monolog"]
	path = Component/monolog
	url = git@github.com:adeira/monolog.git
[submodule "Component/code-quality"]
	path = Component/code-quality
	url = git@github.com:adeira/code-quality.git
[submodule "Component/workflow"]
	path = Component/workflow
	url = git@github.com:adeira/workflow.git
[submodule "Component/presenter-factory"]
	path = Component/presenter-factory
	url = git@github.com:adeira/presenter-factory.git
```

Skvelé je, že má Git vestavěnou podporu pro submoduly, takže to není nic přes ruku. Jen je třeba zapomenout na první část tohoto článku a začít o submodulech přemýšlet úplně jinak. Každý submodul má vlastní složku `.git` a je tedy nutné dělat commity v rámci každého submodulu zvlášť. Naštěstí PhpStorm umí udělat commit hromadně, takže pokud se provádí změna ve všech balíčcích, tak lze vše commitnout najednout. V superprojektu se commitují pouze složky, které obsahují submodul. Ty žijí v rámci Gitu ve speciálním módu `160000` který říká, že se jedná o složku, která ukazuje na konkrétní commit v konkrétním submodulu (jiném repozitáři). GitHub pak vytvoří takto hezké [symlinky do jiného repozitáře](https://github.com/adeira/superproject/tree/master/Component).

> Pamatujte, že zdrojem pravdy je v superprojektu vždy submodul a ten existuje v konkrétním repozitáři! Nemáte tedy k dispozici monolitický repozitář, ale způsob jak ovládat mnoho repozitářů z jednoho místa. Pracovat s těmito repozitáři lze naprosto nezávisle na superprojektu a **nikdy se nedostanete do nekonzistentního stavu**.

Zdrojem pravdy jsou tedy cílové repozitáře, což je super, protože o to jde. Software vydávám jako samostatný balíček odkud si jej mohou uživatelé nebo Composer stáhnout. Zde jsou vlastní testy, readme i verze. Nemělo by se stát, že vydám rozbitou verzi, protože ji vydávám vždy nad konkrétním repoitářem, nikoliv nad monorepozitářem. Superprojekt se nemůže dostat do nekonzistence se submoduly, protože na ně jen ukazuje. To znamená, že neexistuje jeden kód na dvou místech (to třeba v Symfony není pravda).

Git Push ze superprojektu je ideální spouštět s tímto přepínačem:

```
git push --recurse-submodules=on-demand
```

V tomto případě se nejdříve odešlou na vzdálený server submoduly (jen pokud je to potřeba) a až následně se aktualizují ukazatele v superprojektu. O tom jak pracovat se submoduly existuje [dlouhatánský článek](https://git-scm.com/book/en/v2/Git-Tools-Submodules) - pokud chcete Git ovládat ještě více, tak doporučuji pročíst.

Osobně jsem superprojekt dotlačil ještě o kousek dál. Teď už je jedno jestli se bavíme o "split" typu nebo "super" typu - bude to stejné. Všechny balíčky existují v nějaké složce a každý má vlastní Composer závislosti a vlastní testy. Zkombinoval jsem více přístupů jak obstarávat všechny závislosti a nejvíce se mi líbil zbůsob jakým to řeší [beberlei/composer-monorepo-plugin](https://github.com/beberlei/composer-monorepo-plugin). Dělám to tak, že jsem si vytvořil Composer příkaz:

```
composer adeira:collect
```

Tento příkaz projde všechny submoduly a přečte si co mají napsáno v souboru `composer.json`. Následně vygeneruje v hlavním balíčku soubor `composer.json`, který obsahuje všechny požadavky (`require` i `require-dev`) ze submodulů. Podobně to udělá i s autoloadery. Navíc vygeneruje speciální sekci `replace` takto:

```js
"replace": {
	"adeira/code-quality": "self.version",
	"adeira/compiler-extension": "self.version",
	"adeira/monolog": "self.version",
	"adeira/presenter-factory": "self.version",
	"adeira/workflow": "self.version"
}
```

Composer se k tomu potom chová tak, že místo toho aby hledal submodul někde pomocí závislostí, tak sáhne rovnou po submodulu (tak to dělá i Symfony a moc se mi o líbí). K tomu všemu ještě balíčkům přegeneruje soubory `vendor/autoload.php` s tímto obsahem:

```php
<?php return require dirname(dirname(dirname(__DIR__))) . '/vendor/autoload.php';
```

Každý submodul teď tedy funguje (v mém případě) ze superprojektu a lze např. spustit testy napříč celým jmenným prostorem Adeira. Super je, že díky tomu všechny balíčky používají jednotné verze knihoven a nezastarávají. Nedokážu úplně domyslet jaké jsou důsledky mého počínání, ale je to _1)_ nejlepší rozhodnutí co jsem zde udělal nebo _2)_ největší hovadina a budu to muset zrušit. Uklidňuje mě, že tento způsob používají daleko větší projekty a mlaskají si blahem.

Podobně mám udělaný ještě příkaz `adeira:eject`, který aktualizuje verze závislostí v jednotlivých submodulech podle hlavního repozitáře a příkaz `adeira:create`, který vytvoří nový submodul podle připravených šablon.

Věřím, že pokud vydáváte mnoho knihoven, tak vám tento způsob ušetří hodně práce a začnete jej zavádět. Ve výsledku je jedno jakou z cest se vydáte - důležité je, že se vydáte... :)
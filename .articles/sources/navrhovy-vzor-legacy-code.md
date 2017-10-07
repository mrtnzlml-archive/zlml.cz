---
id: 23efba3e-e7da-496e-a75f-5e9f736879e6
timestamp: 1440940560000
title: Návrhový vzor Legacy code
slug: navrhovy-vzor-legacy-code
---
Asi každý se k tomuto návrovému vzoru jednou dostane. Zatím jsem vždy takovou práci striktně odmítal, ale tentokrát šlo o jinou situaci a svolil jsem k poklesu od OOP frameworku ke špagetě. Ačkoliv má pojem "legacy code" celou řadu definic, já osobně jej chápu jako kód, který je prostě starý. Vhodnější by však bylo asi říci, že se jedná o kód se kterým teď musím pracovat, ale nenapsal jsem ho. Zůstaňme však u první definice. Zde platí, že (stejně tak jako jakýkoliv jiný navrhový vzor) obsahuje celou řadu opakujících se poznávacích prvků.

Například mezi vývojáři panuje pozoruhodná pasivita. Nikdo se nepostaví na zadní a nepřijde s něčím novým. Nemyslím novou fičuru, ale nějakou systémovou věc. Na druhou stranu se to dá pochopit. Je to ta nejdražší změna a zase až tak ničemu to z vnějšího pohledu neprospěje. Nepovažuji to za správný přístup, ale taková je asi realita. Bohužel se pak často argumentuje (totální) zpětnou kompatibilitou. Dále tento návrhový vzor předpokládá, že se používá nějaký vypíčený verzovací systém a nikoho to netrápí (viz pozoruhodná pasivita vývojářů). O coding standardu ani nemluvě a globální prostor je samozřejmostí. No a pak samozřejmě klasické věci jako RY přístup (opak [DRY](https://cs.wikipedia.org/wiki/Don%27t_repeat_yourself) - Don't Repeat Yourself), šablonovací systém (pokud vůbec existuje) je stejně debilní jako ten verzovací a v neposlední řadě neotestovaný/neotestovatelný kód.

V mém konkrétním případě jsem se musel potýkat ještě s něčím. Jednak jsem se musel poprat s opačným smýšlením vývojářů. Takže například zatímco všude se direktiva `magic_quotes_gpc` [vypínala](http://php.vrana.cz/vypnuti-magic_quotes_gpc.php), zde se globálně zapínala atd. No a pak samotný globální prostor to je písnička sama pro sebe. Myslel jsem si, že to až takový problém nebude, ale neuvědomoval jsem si, co to vlastně obnáší. Pokud stejně jako já nikdy globální proměnné nepoužíváte, zde je příklad na připomenutí.

Jakákoliv globální proměnná, která není nijak dále zabalená je automaticky globální:

```php
$x = 'y';
dump($GLOBALS['x']); //y
```

To dává smysl a není na tom nic divného. Za mnohem větší problém však považuji fakt, že to funguje i obráceně:

```php
$GLOBALS['x'] = 'y';
dump($x); //y
```

Proč je to problém? Protože druhý případ je striktně závislý na použitém kontextu. Tím pádem tato pseudoglobální proměnná funguje ve špagetě, ale když chcete takový kód jinak uspořádat a nedej bože ještě obalit, tak je to problém. Vzhledem k tomu, že bylo mým úkolem integrovat [Nette Framework](https://nette.org/) do takového systému, musel jsem trošku upravit start aplikace a tím jsem hodně věcí rozbil.

![](https://zlmlcz-media.s3-eu-west-1.amazonaws.com/d6ca5ea3-5c1a-43af-8488-73d4fae836f1/strip-wordpress-550-finalenglish.jpg)

# Jak jsem na to šel

Nebudu zde rozebírat přesně důvody proč jsem to udělal tak a ne jinak. Většinou mě to k tomu řešení jasně dovedlo, protože dělat to jinak by bylo nesmyslně složité - pokud vůbec možné. Berte to jako inspiraci. Myslím si, že se to bude ještě nekomu hodit, protože je tento návrhový vzor rozšířen více, než si přiznávám.

V první řadě bylo nutné do projektu přidávat závislosti pomocí Composeru. Jednak se tak projekt trošku vyčistil od zbytečně nakopírovaných knihoven a potom jsem mohl s výhodou používat jednotlivé Nette komponenty. Pak je velmi důležité celý projekt poznat trochu hlouběji. Na to není vždy čas. Začal jsem proto tak, že jsem napojil na systém jednodušší části frameworku jako je třeba Tracy, Cache, Utils, RobotLoader atd. S tím se samozřejmě svezlo několik úprav, jako je například zapnutí error reportingu nebo vypnutí zahazování výjimek a další podobné hovadiny. Samotné zapnutí error reportingu je neskutečný oser, protože se tím ukáže, jak se daná aplikace hrozně sype ([však jsou to jen notices](https://media.giphy.com/media/11c2hRHwmvgFOg/giphy.gif), co se může stát). Každopádně už třeba použitím cache na správných místech a úpravou několik funkcí se aplikace rozjela daleko rychleji.

Další čeho bych se rád zbavil jsou `mysql_*` funkce a nahradil je PDO. To není úplně jednoduchý úkol a pořád je to "in progress". Zde jsem zvolil NDB, ale nejsem si tou volbou vůbec jistý. No a pak nastal čas, kdy je třeba přistoupit k hlubší integraci frameworku. To jsem chtěl udělat jako štít před celou aplikací. Tím jsem samozřejmě polovinu věcí rozbil, ale naštěstí už to tak nějak funguje. Co to vlastně znamená? V první řadě například startování aplikace z jednoho místa (což nebylo normální a rozbilo to všechno) a potom napsání LegacyPresenteru, který se stará o zpětnou kompatibilitu se starým jádrem (což zase rozbilo pseudoglobální kontext). No a potom bylo potřeba vyřešit také routování. To však ve výsledku bylo velmi triviální a stačilo napsat několik základních pravidel, za která se schová jakákoliv URL v systému. Jednoduchá implementace takového presenteru může vypadat třeba takto:

```php
//dodatečná nastavení ve startup()

public function renderDefault($fakePath = NULL)
{
    if (NULL !== $fakePath) {
        if (file_exists($file = SITE_ROOT . DS . $fakePath)) {
            require $file; //bacha na Local File Inclusion
        }
    }
}
```

Tato implementace vlastně kopíruje původní chování. Napsání routovacích pravidel je fakt jednoduché:

```php
$router[] = new Route('<? index(\.html?|\.php)>', 'Legacy:default', Route::ONE_WAY);
$router[] = new Route('[<fakePath .+>]', 'Legacy:default');
```

Mnohem zajímavější je však implementace Smarty šablonovacího systému. K tomu je vhodné napsat si vlastní implementaci render metody objektu [Template](https://api.nette.org/2.3.5/source-Bridges.ApplicationLatte.Template.php.html). Zde se rozhodne, jak se bude šablona vykreslovat. V mém případě jestli pomocí Latte, nebo Smarty. Je samozřejmě nutné upravit i TemplateFactory hlavně kvůli [této řádce](https://api.nette.org/2.3.5/source-Bridges.ApplicationLatte.TemplateFactory.php.html#56).

# Špatné pořadí

Všechno špatně. Teď vím, že jsem měl začít obráceně a nejdříve si na to napsat testy. Alespoň dodatečně (klasicky po prvním problému) jsem si na to ještě napsal jednoduché scénáře pro akceptační testy v Codeception. A pak jsem si měl stát za svým a neústupně odstranit všechny weird věci, protože ty prostě nejsou kompatibilní s moderním frameworkem a způsobuje to jen nepředvídatelné problémy.

Jsou vlastně nějaké výhody takové integrace frameworku do starého systému? Vyjma těch klasických, které přináší framework sám o sobě, je teď možné psát nové věci Nette stylem a využívat všechny možnosti (hlavně asi DIC a Latte). Se starým kódem se kromě vyčištění od hovadin vlastně zase až tak dít nic nebude a je možné jej přepisovat do nového kabátku. Osobně bych nejraději odstranil globální proměnné úplně, to je ale na tak velkém projektu skoro nemožné.

No a pak je zde psychická stránka věci. Jak se budou tvářit ostatní vývojáři až zjistí, že framework nemá jen pomáhat, ale i omezovat, aby programátor nedělal píčoviny? Najednou je programování náročnější. Spousta objektů, žádný `$GLOBALS`, dependency injection... Skvělé však je, že má takovýto úkol i svá nesporná pozitiva. Jedině zde budete pracovat s frameworkem skutečně po částech a tím spíš se nechají jednotlivé části poznat.

Máte také nějaké zkušenosti s legacy projektem?
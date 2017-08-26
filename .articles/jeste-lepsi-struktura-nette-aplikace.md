---
id: 8474ac83-61b4-4afe-b0d5-ca7ba4e62501
timestamp: 1444573684000
title: Ještě lepší struktura Nette aplikace
slug: jeste-lepsi-struktura-nette-aplikace
---
Každý, kdo postavil pár aplikací, musel vždy řešit ten samý problém. Jakou strukturu by měla aplikace mít? A co když se začne projekt rozrůstat? Měl bych se držet toho jak to dělá [sandbox](https://github.com/nette/sandbox) (resp. [web-project](https://github.com/nette/web-project))? Postupem času jsem dokonvergoval k relativně přijatelnému výsledku a vzhledem k tomu, že projekt na kterém jsem to poprvé pořádně rozjel byl ukočen, [rozhodl jsem se jej zveřejnit](https://github.com/mrtnzlml/CMS-lite). Už je to sice nějaký čas, ale v době největšího vrcholu tohoto projektu jsem jej považoval za takovou osobní špičku. A to hned z několika důvodů. K tomu se ale dostanu postupně. A vezmu to pěkně od těch nejmenších částí.

# Presentery a komponenty

U presenterů se mi vlastně docela líbí jak to dělá sandbox. Ve složce presenterů jsou logicky presentery a také složka `templates`, která obsahuje šablony právě k těmto presenterům:

```
presenters/
    templates/
        Homepage/
            default.latte
        @layout.latte
    BasePresenter.php
    HomepagePresenter.php
```

Dříve to bylo jinak. Šablony a presentery měl dříve sandbox na stejné úrovni, ale souhlasím s tím, že šablony patří spíše (a poměrně úzce) k presenterům. Je tedy nutné zmínit, že díky tomu jak je Nette framework chytrý, tak je v podstatě jedno jakou bude mít projekt strukturu, protože bude fungovat vše. Za chvíli se však začne vše nabalovat a bude třeba neustále udržovat pořádek. Proto je nutné mít jasno i v takto triviálních otázkách.

Komponenty se od presenterů tolik neliší. Spíše naopak. Komponenty mohou být poměrně komplikované, ale rád je dělám co nejjednodušší. U komponent používám několik návrhů. Pro velmi triviální komponenty zachovávám plochou strukturu:

```
ContactForm/
    ContactForm.latte
    ContactForm.php
```

Je totiž otrava vytvářet spoustu zbytečných složek. To platí i pro soubory. Proto jsou generované továrničky vždy k nalezení pod třídou komponenty v jednom a tom samém souboru. Jakmile se však komponenta jen o trošku zkomplikuje, automaticky přepínám do klasického presenterového stylu:

```
ContactForm/
    providers/
        IContactFormTemplateProvider.php
    templates/
        ContactForm.latte
    ContactForm.php
```

Dobře, základní stavební kameny jsou položeny. Co by však mělo být okolo. A kde jsou vlastně položeny? Inu pojďme na to opět postupně.

# Supercore věci

Fakt nevím jak to nazvat jinak, protože ke core záležitostem se ještě dostanu. O co tedy jde? Jedná se o části aplikace, které tvoří to nejzákladnější jádro. Jádro, na které je pak možné napojovat další věci. Tuto část aplikace nechávám ve složce `app`:

```
app/
    AdminModule/
        presenters/ <-- viz první ukázka (je zde jen BasePresenter)
    AuthModule/
        presenters/ <-- jen SignPresenter
    FrontModule/
        presenters/ <-- viz první ukázka (Base, Contact, Homepage)
    components/
        ContactForm/ <-- také už známe (viz druhá ukázka)
        AControl.php
    config/
    extensions/
    bootstrap.php
```

Jak je vidět, tak všechny moduly obsahují jen kritický základ. Žádné další presentery. Tak kde je zbytek? Zbytek se nechází v rootu aplikace, konkrétně ve složkách `custom` a `src`. Je celkem jedno jaký je název těchto složek, vtip je v tom nějaké mít a vše sem přesunout. Důvod proč jsou dvě je prostý. Zatímco v `src` jsou části aplikace, které tvoří jádro (tedy spoustu funkčnosti), v `custom` jsou velmi podobné částí aplikace, bez kterých lze však žít. Původní myšlenka byla taková, že se pak custom složka zruší a vše v ní se velmi elegantně rozpadne na composer balíčky. Obě složky jsou však strukturálně stejné, proto budeu řešit jen `custom`.

# Business logika

Ok, to jsem také nazval pěkně debilně. Alespoň však vysvětlím jednu důležitou věc, se kterou jsem v začátcích bojoval a kterou je potřeba se odnaučit. Sadbox vždy totiž vedl k takovéto podobné struktuře:

```
app/
    config/
    forms/
    presenters/
    model/
    router/
```

To nikomu nemám za zlé. Je to jednoduše pochopitelné a to je dobře. Takže s tím vlastně spíš souhlasím. Problém je v tom, že u rozrůstající aplikace už to začíná být děsný mrdník, protože `presenters` najednou obsahují všechny presentery a `model` obsahuje všechnu logiku. Ale v tom aby se prase vyznalo. Tento efekt se nechá trošku umírnit rozdělením aplikace na moduly a s tím už jsem byl (a vlastně do dneška jsem) spokojen. Jenže co s tím modelem? Fuck model! Rozdělte si model podle logických částí, které na sobě nejsou závislé a vše oddělte. Třeba takto:

```
custom/
    Error/
    Eshop/
    Files/
    Notes/
    Pages/
```

Jak řekl kdosi moudrý, existují dva nejnáročnější problémy v programování a to správná invalidace cache a pojmenovávání věcí. Naprosot s tím souhlasím. Moc mi to nejde, ale mělo by být zřejmé, že jsou zde části, které se starají (výhradně) o eshop, o poznámky, stránky atd. Pojďme se tedy zanořit hlouběji:

```
Pages/
    AdminModule/
        presenters/
            CategoryPresenter.php
            PagePresenter.php
    components/
        PageForm/
        PagesGrid/
    DI/
    FrontModule/
    listeners/
    Category.php
    Page.php
    ...
```

A voilà, máme tu zase strukturu složky `app`. Nebo alespoň její obdobu. A v tom je síla toho návrhu. Mělo by už teď být jasnější, proč jsou v `app` právě ty věci co tam jsou. Celém vždy bylo mít v systému místa, které obsahují velmi podobné věci, ale nic dalšího. Drobné niance se zde najdou, to je jasné, ale základ zůstává. Jenže jak to sakra funguje?

# Jak to sakra funguje

Právě teď je ten správný čas [proklikat si celý systém](https://github.com/mrtnzlml/CMS-lite). Je zřejmé, že už je to trošku komplikovanější a samo od sebe to fungovat nemůže (ani to není žádoucí). Zkušenější už tuší, že celé kouzlo je ve složce `DI`. Zde je tedy mé další doporučení. Až rozsekáte aplikaci do komponent, udělejte to samé s funkcionalitou. A víte co, udělejte to se vším co spolu nějak logicky souvisí. Proto jsem do složky `custom/Pages` umístil vše co patří ke stránkám. Komponenty, doctrine entity, servisní třídy, fasády, ale také presentery. Prostě všechno. Dělejte to tak dlouho, dokud v `app` nezůstane nic.

Tento způsob však s sebou nese celou řadu úskalí. Prvně je to komplikované. A pak je třeba vše napojit. Existují dva způsoby, které mi přijou v pořádku. První je poněkud agresivní, ale jednoduchý. Vychází vlastně z myšlenky [Flame\Modules](http://flame-org.github.io/Modules/). Napíšete si nějaké rozšíření, které bude implementovat nějaký interface. Třeba `IFaviconProvider`. Pak je třeba mít (právě v supercore) rozšíření, které takový interface najde a při vytváření DIC zpracuje. Hodně toho využívají šablony (`custom/Versatile/DI/VersatileExtension.php`). Nebezpečí je však v tom, že se to prostě stane jakmile přidáte toto rozšíření do aplikace. Není zde moc rozumná možnost jak třeba rozšíření deaktivovat. A ještě komplikovanější je pak při vytváření DIC přeba automaticky spustit nějaký SQL dotaz.

Proto je zde druhý způsob (který jsem pořádně nestihl dodělat). Využívá jej například `\Eshop\DI\EshopExtension`. To implementuje `ICustomExtension`. Jiné (supercore) rozšíření se toho chytí a udělá pouze to, že jej zobrazí v administraci včetně potřebných odadtečných informací. Stejně tak jako to dělá WordPress. Uživatel zde může kliknutím modul nainstalovat, což se přesně u eshopu děje a spustí se tak celá řada komplikovaných operací, které tento modul nainstalují. Jedná se zejména o předání URL adres, nastavení ACL, zaregistrování položek do menu atd. Elegantně se tak celý systém připraví a díky tomu, že dojde k registraci do DIC, není důvod k tomu, aby se s narůstajícím počtem modulů systém nějak dusil. Prostě se chová jako jakákoliv jiná velká aplikace. Nic není hledáno a řešeno dynamicky za běhu aplikace. Druhý krok je už pak dodělat instalace modulů ze vzdáleného repozitáře, ale to už je jednoduchý úkol.

A je to. Elegantní instalační systém pluginů pro vaší Nette aplikaci.

# Další zajímavé vlastnosti systému

Takže to máme peckovou strukturu aplikace, kterou je velmi jednoduché udržovat a rozšířovat + automatickou registraci modulů (pluginů chcete-li). A to jsem teprve na začátku. Proto už jen bodově vypíchnu a připomenu některé zajímavé věci, které všem dávám k dispozici.

1. Každé rozšíření je v Nette nutné registrovat do konfiguračního souboru. To by s tím ale nešlo nělat takové švíky. Proto jsem napsal `\App\Extensions\CoreExtension`, které to dělá automaticky. Není to zrovna ukázka čistého kódu, ale svůj účel to plní dobře. Už touto vlastností jste několik mil před konkurencí... (-:
2. Vzpomínáte si na [Hierarchický router](hierarchicky-router)? Tak i ten je zde v celé své kráse. Jen pro připomenutí. Je možné měnit si URL adresy jak chcete, nikdy nepřijdete o ty staré a výkonově to nestojí nic navíc.
3. [Dynamické routování URL adres](dynamicke-routovani-url-adres). To je další fičura, kterou jen tak někdo nemá. Nebo snad ano? Použijte ji. Vybudujte něco úžasného.
4. A co takhle Fixtures. [Vzpomínáte si](fixnete-si-databazi)?
5. Všimněte si, že hodně rozšíření obsahuje jakési providery. Je tak možné jednoduše třeba zaměnit šablonu kontaktního formuláře, nebo navigace. Obdobně mohou rozšíření registrovat vlastní styly i javascriptové skripty.
6. K dispozici jsou Doctrine migrace. K dispozici jsou v nabídce přes `php index.php`. Samotné migrační skripty jsou pak v `migrations` složce.

Je toho fakt ranec, co pouštím na obdiv i kritiku. Proto ještě stručněji přehled technologií, které v projektu naleznete:

- grunt + grunt-contrib packages (concat, copy, cssmin, less, uglify)
- bootstrap, nette.ajax.js, nette-forms, jquery, selectize
- nette (application, caching, DI, finder, forms, robot-loader, security, utils, ...)
- latte, tracy, texy, webloader, minify, faker, secured-links
- kdyby (doctrine, annotations, console, events, translation, autowired, monolog, ...)
- doctrine (data-fixtures, migrations, ORM, ...)
- testbench, nette\tester

A to jsem zde ještě nenapsal vše. Mrkněte se na náhled, nejedná se jen o nějaký marný pokus:

![](https://zlmlcz-media.s3-eu-west-1.amazonaws.com/9b3c176d-4884-45c5-95c3-53cac2999d0f/admin.png)

# Instalace systému

Bohužel jsem nevychytal všechny mouchy, půlka věcí zůstala nerozdělána a celý materiál je spíše pro inspiraci. Pokud by si to však někdo chtěl rozjet, dávám k dispozici také poněkud složitější návod na instalaci (viz readme):

- Nainstalujte si [GIT](http://git-scm.com/)
- `git clone https://github.com/mrtnzlml/CMS-lite.git`
- Nainstalujte si [Composer](http://getcomposer.org/)
- `composer install` (natáhne PHP závislosti)
- Vytvořte si prázdnou MySQL databázi
- Přejmenujte `config.local.neon.dist` (v app/config) na `config.local.neon` a nastavte přístupové údaje k databázi
- `php index.php orm:schema-tool:create` (vygeneruje strukturu databáze)
- `php index.php cms:fixtures:load` (našte základní data, teď už by měla aplikace fungovat)
- Nainstalujte si [Bower](http://bower.io/), popř. [npm](https://www.npmjs.com/) je-li třeba
- `bower install` (fetches JS dependencies)
- Nainstalujte si [Grunt](http://gruntjs.com/)
- `grunt` (připraví JS, CSS, fonts, ...)

Každý příkaz by měl být spouštěn z rootu webu. Výjimku tvoří příkazy obsahující `index.php`. Ty je třeba spouštět ze složky `www`. To dá asi rozum.

A na závěr. Jsem realista. Nepředpokládám, že by se projektu někdo doprovolně chytil a nakopl mě, aby v něm pokračoval aktivněji. Zatím jej spíš nikdo nepochopil a musel jsem si protrpět i pár klacků pod nohama. Proto jej dávám k dispozici jako inspiraci pro ostatní. Nemám strach, že by si to někdo přivlastnil, nebo na tom zbohatl. To si spousta firem (lidí) neuvědomuje a tak nikdo raději nezveřejňuje nic. Zveřejňujte, předávejte znalosti - nepřijdete o ně. Nicméně v rámci gentlemanské dohody bych rád vyměnil své předané znalosti za hvězdičku u tohoto nového repozitáře. Nic víc nežádám.

<iframe src="https://ghbtns.com/github-btn.html?user=mrtnzlml&repo=CMS-lite&type=star&count=true&size=large" frameborder="0" scrolling="0" width="160px" height="30px"></iframe>

Pokorně děkuji.
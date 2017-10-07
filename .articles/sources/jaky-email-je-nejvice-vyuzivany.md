---
id: 3d580b09-b622-4a72-a912-a2205994b051
timestamp: 1392999129000
title: Jaký email je nejvíce využívaný?
slug: jaky-email-je-nejvice-vyuzivany
---
Včera jsem psal o bezpečnostní chybě, která umožňuje získat podle mého názoru nezanedbatelně velký vzorek emailových adres. Krom toho, že bych byl rád, aby byla tato chyba opravena, nemám co jiného s touto kolekcí adres dělat. Tak jsem se rozhodl vytvořit nějakou statistiku. Svým způsobem se totiž jedná o dosti specifické uživatele, protože se pravděpodobně jedná z naprosté většiny pouze o ČR a SK uživatele, takže kdo čeká, že bude např. gmail umístěn nějak dobře, možná bude překvapen.

A protože po diskusích krouží mnoho rádoby matematiků, rád bych teď přesně definoval všechny vstupní a výstupní hodnoty včetně jejich chyb, čímž doufám minimalizuji to, že výsledky někdo špatně pochopí. Všechny data jsou brány z kolekce čítající **384 392 unikátních** emailových adres s tím, že uvažuji zejména u rozložení poskytovatelů emailových schránek chybu 1%. Je to dáno tím, že vstupní data nejsou úplně korektní a ukazuje se, že se dost často vyskytují emaily jako `@seznam.czsms`, `@seznam.cztel`, `@seznam.czwww`, `@seznam.czweb`, `@senam.cz` atd. Většinu takových prohřešků jsem se snažil eliminovat, každopádně není to dokonalé. Osobně bych chybu odhadoval na desetiny, možná ani ne setiny procenta, ale raději ji nechávám větší. Tato chyba je v prvním grafu také graficky znázorněna. Zajímavé je také to, že někteří lidé evidentně chápu input pro zadání emailu jako deník, takže jsou schopni napsat do tohoto řádku všechny své emailové adresy. Stojí za zamyšlení jak správně navrhovat formuláře... Dále jsou pak z výpočtů zastoupení četnosti znaků prvních písmen emailových adres vypuštěny čísla, takže tato dílčí statistika počítá s o něco menší vstupní kolekcí 382 338 emailových adres.

# Rozložení poskytovatelů emailových schránek

Následující graf ukazuje počet emailových adres příslušejících ke konkrétní doméně. Konkrétně je zobrazeno prvních 20 největších poskytovatelů a i tak již mají poslední méně než jedno procento velikosti prvního, tedy Seznamu. Ten je v českém zastoupení naprosto bezkonkurenční. Všímejte si prosím takových věcí. Většina programátorů se totiž potkává pouze s lidma "od gmailu", ale to je na českém trhu pouze minoritní složka. A je to tak u všeho. Je až překvapivě obrovský nepoměr mezi tím co si většina lidí myslí a skutečností. A tento vzorek již považuji za dostatečnou skutečnost.

<iframe height=371 width=600 src="//docs.google.com/spreadsheets/d/1nWEt95Hd8CFxrwylUJr8uUFtceN6QiGMLL3JilW3ETQ/gviz/chartiframe?oid=744207493" seamless frameborder=0 scrolling=no></iframe>

Kolikrát jsem slyšel, že jsou Centrum a Atlas mrtvé projekty. To už ani nemá smysl počítat, ale reálně se ukazuje, že tomu tak vůbec není a těmto číslům momentálně věřím, protože už je zde přehazuji několik desítek hodin... (-: Bohužel nemám představu o tom, kolik existuje emailových adres v ČR, ale veřím, že se tvarově graf moc nepohne. Pouze se bude měnit počet na svislé ose.

# Další zajímavá data

Další graf ukazuje skutečně velkou hloupost. Ani nevím proč jsem jí vlastně dělal. Jde o početné zastoupení prvních znaků emailových adres. To jsou ty modré pruhy. Červené pruhy jsou pak předpokládaná četnost znaků pro českou abecedu podle [Jana Králíka .{target:_blank}](http://www.czech-language.cz/alphabet/alph-prehled.html). Není to poprvé co tuto tabulku četností používám a dá se říci, že s ní souhlasím. Každopádně mám v plánu tyto četnosti ověřit a zaktualizovat, takže se možná tento graf časem malinko pozmění.

<iframe height=371 width=600 src="//docs.google.com/spreadsheets/d/1nWEt95Hd8CFxrwylUJr8uUFtceN6QiGMLL3JilW3ETQ/gviz/chartiframe?oid=1555577201" seamless frameborder=0 scrolling=no></iframe>

Další informací, která již však nemá pevně uchopitelný základ je počet TLD. Lze tedy pouze říci, že ze vzorku zkoumaných dat, tedy ze vzorku emailových adres českých uživatelů mají největší zastoupení koncovky `.cz` (290311), poté `.sk` (88764) a další v pořadí je `.com` (5183), která se však svojí četností již poněkud mimo hru. Zajímavé teké je, že většinu překlepů dělají češi. To může být tím, že jsme prostě nepozorní, nebo jsou programátoři lajdáci. Tato informace je založena na počtu korekcí doménových názvů.

Poslední již nikterak využitelnou informací je délka adres. Nejdelší adresy jsou `butovice.zlicine.tel.728222069.pouzite.kalhotky@...`, `www.malirstvi.tym.czemail.malirstvi.hruby@...` a `martin.59kenvelo400500600300700800900201@...` Zakrývám alespoň domény, aby někdo neprskal, když už proti tomu tak zbrojím. Naopak nejkratší je adresa, která má se vším všudy 8 znaků: `in@.....`.

Zajímá vás ještě nějaká informace, která se dá z této kolekce emailových adres získat?
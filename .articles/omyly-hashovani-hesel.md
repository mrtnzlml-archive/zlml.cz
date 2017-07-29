Někdy minulý rok jsem si četl prezentaci [Michala Špačka .{target:_blank}](http://www.michalspacek.cz/) o [hashování hesel .{target:_blank}](http://www.slideshare.net/spaze/hashe-hesla-develcz-2013) a byl jsem z toho poněkud zklamán. Naprosto souhlasím se vším co tam je, přesto však nemám rád, když se dojde k závěrům, které sice na první pohled dávají smysl a fakt dobře se tak dá argumentovat, ale ve výsledku jsou podle mého názoru některé opravdu chybné. Nedávno kolem mě tato prezentace proplula znova a protože již mám celkem setříděné myšlenky, rozhodl jsem se je sepsat.

Tímto článkem nehodlám hatit již zmíněnou prezentaci. Naopak se chci opřít do všech prezentací a výstupů, které něco tvrdí a není to tak docela pravda. Zároveň je také nutno říct, že nebudu řešit funkce typu MD5, protože doufám, že všichni v dnešní době vědí, že funkce MD5 prostě není určena na hashování hesel. Přesto si neodpustím několik vět i o konkrétních implementačních problémech.

Omyl první
==========
Když jsem se opět dožadoval matematického důkazu o problému cyklického hashování, byl jsem dokázán na [stackoverflow .{target:_blank}](http://stackoverflow.com/questions/348109/is-double-hashing-a-password-less-secure-than-just-hashing-it-once/17396367#17396367), což mě mělo uspokojit. Četl jsem to pozdě v noci, takže jsem to nechal na ráno a ani tak jsem s tím nesouhlasil.

V podstatě se jedná o popsání preimage útoku, který se snaží najít stejný hash jako je hash známý a tím získat původní (nebo jinou fungující) hodnotu hesla, tedy v tuto chvíli jediný zajímavý způsob. Celý důkaz toho, že cyklické hashování není dobrý nápad je prováděn na vlastní funkci a směšně malé množině vstupů. To je první divná věc. Mnohem divnější však je závěr pokusu, který tvrdí, že nekonečný vstup lze namapovat na konečnou množinu. Jedná se tedy o surjektivní zobrazení první množiny na druhou, kdy se každý prvek z první množiny namapuje na všechny prvky menší výstupní podmnožiny. A právě v tomto kroku vidím celou teoretickou úvahu jako chybnou. Žádná hashovací funkce totiž nepočítá s libovolným, nebo dokonce s nekonečným vstupem. Když to rozvedu i na konkrétní funkci MD5, pak nekonečný počet vstupů mapuji na 2^128 výstupů. Reálně (což je to co nás primárně zajímá) však mapuji 2^64 vstupů na 2^128 výstupů (čti bitů). V tu chvíli se však bavíme o injektivním zobrazení, což bylo v původním textu odsouzeno. Jak jsem k tomumo názoru došel? Vycházím z [RFC 1321 - The MD5 Message-Digest Algorithm .{target:_blank}](http://www.faqs.org/rfcs/rfc1321.html)

> A 64-bit representation of b (the length of the message before the
> padding bits were added) is appended to the result of the previous
> step. In the unlikely event that b is greater than 2^64, then only
> the low-order 64 bits of b are used. (These bits are appended as two
> 32-bit words and appended low-order word first in accordance with the
> previous conventions.)

Pak už je jen malý krůček k tomu uvědomit si, že 2^64 bitové heslo je jinak řečeno něco kolem 2 exabajtů, což je tak trošku hodně i na uložení, natož na zapamatování. A i kdybych to spočetl blbě - jakože doufám, že ne - několik řádů sem tam je úplně jedno, protože množina na kterou se to mapuje je daleko větší. Stejně tak mi přijde naprosto komická tato ukázka:

```
$output = md5($input); // 2^128 possibilities
$output = md5($output); // < 2^128 possibilities
$output = md5($output); // < 2^128 possibilities
$output = md5($output); // < 2^128 possibilities
$output = md5($output); // < 2^128 possibilities
```

Přepíšu to jinak:

```
$output = md5($input); // 340282366920938463463374607431768211456 possibilities
$output = md5($output); // 340282366920938463463374607431768211455 possibilities
$output = md5($output); // 340282366920938463463374607431768211454 possibilities
$output = md5($output); // 340282366920938463463374607431768211453 possibilities
$output = md5($output); // 340282366920938463463374607431768211452 possibilities
```

Vycházíme tedy z 340 undecilionů 282 decilionů 366 nonilionů 920 octilionů 938 septilionů 463 sextilionů 463 quintilionů 374 quadrilionů 607 trilionů 431 bilionů 768 milionů 211 tisíc 456 možností. Jasné? Tuto teorii tedy považuji za čistě teoretickou. Klidně si to heslo zašifrujte undecilionkrát... Ostatně stejný problém byl při šifrování vždy. Klidně se mohlo stát, že klíč od zprávy zašifrované v Enigmě (nebo kdekoliv jinde) uhodnou. Ale nedělalo se to, protože je to prostě jen papírový nesmysl. Ostatně i když se to stane, tak je to prostě debilní smůla, jenže věří snad ještě někdo tomu, že se to stane u celé databáze?

Omyl druhý
==========
Nyní budu předpokládat, že bude nyní mým cílem zjistit skutečně takové heslo, jaké bylo před zahashováním. Úplně tím tedy odbourám fakt, že dva různé texty budou mít stejný hash. To se může stát, druhá možnost však bude svým charakterem tak úplně jinde, že ji stejně nepůjde např. při přihlašování použít. Pokud tedy chci zjistit heslo z hashe u kterého vím, že bylo několikrát hashováno, nezbývá mi, než jít postupně z hashe až k heslu:

```
7eaefb28c9c3fe4be6997cc5b7fb599f // původní hash
92b7db0f6d7348d91e90651d31ff9e71
651a9c9c86f3116a53e2bb6e80bfdf69
1b929b62a2c822c4a59e688fde2a3a0b
955db0b81ef1989b4a4dfeae8061a9a6
heslo // konec hledání, toto již není MD5
```

Jenže jaké teď skutečné heslo? Je to "heslo"? Pokud bych byl chytrý a uměl si zapamatovat 32 znaků, moje heslo by určitě bylo "1b929b62a2c822c4a59e688fde2a3a0b" a jen bych zmátl všechny okolo. A potom je celkem sranda to, když se vrátíme k čistě teoretickému uvažování a řekněme, že se může stát, že dva hashe budou totožné:

```
1b929b62a2c822c4a59e688fde2a3a0b // původní hash   <-
... // dlouhá série hashů vedoucích k opakování     |
651a9c9c86f3116a53e2bb6e80bfdf69                    |-- stejné hashe
1b929b62a2c822c4a59e688fde2a3a0b   <-----------------
955db0b81ef1989b4a4dfeae8061a9a6
heslo // konec hledání, toto již není MD5
```

Věřte, že mé heslo je teď "651a9c9c86f3116a53e2bb6e80bfdf69". Při pokusu o zjištění původu hashe mám dvě možnosti. Mohu najít takový hash, který je před tím prvním, ale jiný než "955db0b81ef1989b4a4dfeae8061a9a6", nebo právě "955db0b81ef1989b4a4dfeae8061a9a6". V prvním případě je to stejné jako předchozí případ. V druhém však naleznu nejdříve jinou shodu a nejen že dojdu k jinému heslu, ale také úplně přeskočím to správné heslo. Takže v určitém případě může být opakované hashování dokonce ještě bezpečnější! Opět je to pouze teorie, ale chci tím ukázat, že některé argumenty mohou být sice silné, mají však vždy i obrácenou stranu, která není o nic slabší...

Chtěl jsem tedy vyvrátit několik zažitých předpokladů, což se mi doufám podařilo. Musím však dodat nesmírně důležitou věc. Neobhajuji zde použití MD5 ani jiné podobné funkce (která tak jako MD5 není k hashování hesel určena). Používejte spíše funkce, které mají složitou výpočetní náročnost a jejich výstup je pro dva stejné vstupy různý. Takovou dobrou funkcí je pro PHP funcke `password_hash`, která byla vytvořena právě kvůli tomu, že v tom programátoři dělají neskutečný bordel. Použití je úplně jednoduché:

```php
<?php
echo password_hash('heslo', PASSWORD_DEFAULT);
```

Tato funkce momentálně používá bcrypt, do budoucna je možné použít konstantu PASSWORD_BCRYPT, protože defaultní konstanta může způsob šifrování změnit na nějaký lepší. Stejně jednoduché je i ověření hesla:

```php
if (password_verify('heslo', '$2y$10$2YOiYB9vFd11vTRBtqqKE.TnrT1ydXuCGsSHXbAKRvUgnpE9VaoES')) {
    echo 'Password is valid!';
} else {
    echo 'Invalid password.';
}
```

Tyto funkce, stejně tak jako dobře použitý kompatibilní `crypt` považuji za naprosto dostatečná řešení a víc se o tom není třeba již <s>nikdy</s> bavit. Tečka.

<span style="color:green">A pod tečkou ještě něco. Čím musím napravit nešťastně volená slova v předchozí větě. Tím že se o tom není již třeba nikdy bavit je myšleno to o čem se ve větě píše, tedy že bcrypt funkce považuji za naprosto dostatečné (v současné době i blízké budoucnosti) a za tím si stojím. Neznamená to však, že teď házím za hlavu celou tuto problematiku. Pokud bych měl tedy předchozí větu opravit, napsal bych asi, že tyto funkce považuji za naprosto dostatečné, nicméně stále má smysl tuto problematiku řešit, protože věřím, že v zřejmě ne malém horizontu let bude i tato funkce nedostatečná. Teď ale není.</span>
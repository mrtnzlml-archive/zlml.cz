---
timestamp: 1353707019000
title: Výpočet mediánu
slug: vypocet-medianu
---
# Zadání


Najděte v dostupné literatuře nebo vymyslete co nejlepší algoritmus pro výpočet mediánu.
Nezapomeňte na citaci zdrojů. Kritéria kvality v sestupném pořadí jsou: výpočetní složitost, 
jednoduchost a implementační nenáročnost, paměťová spotřeba.

# Definice


Medián je hodnota, která dělí seřazenou posloupnost na dvě stejně velké (co se množství týče)
části. Důležitou vlastností mediánu je skutečnost, že není ovlivněn žádnou extrémní hodnotou,
jako je tomu například u průměru.

# Analýza problému


Existuje několik způsobů jak daný problém vyřešit. První řešení bude velmi přímočaré. Jedná se
totiž o způsob, který zřejmě napadne každého jako první.

Přímočaré řešení
----------------

Toto řešení vlastně ani není tak hledání mediánu jako hledání algoritmicky nejrychlejšího způsobu
jako seřadit danou posloupnost čísel, protože pokud již máme seřazenou posloupnost, stačí zvolit
prostřední prvek a získáme požadovaný medián. Tento poslední krok můžeme přirozeně vykonat
se složitostí O(1). Seřadit posloupnost je možné lineárně logaritmickou složitostí O(NlogN) avšak
s dodatečnou pamětí. Dodatečné paměti je samozřejmě možné se vyhnout, například použitím
řadicího algoritmu Quicksort, vystavujeme se však nebezpečí kvadratické složitosti v nejhorším
případě.

Při zpětném pohledu je jasné, že nejvíce času strávíme řazením posloupnosti. Přitom řazení
nebylo v zadání. Je to opravdu nutné? Následující algoritmy uvažují vstupní neseřazenou posloupnost
stejně jako přímočaré řešení, ale nebudou vynakládat všechen svůj drahocený čas k
řazení.

Algoritmus FIND
---------------

Metoda FIND je mnohem promyšlenější. Využívá techniky "rozděl a panuj", což je samo o sobě
velmi silná zbraň. FIND se chová velmi podobně jako již zmíněný Quicksort (oba algoritmy vymyslel
Tony Hoare) s tím, že hledá k-té nejmenší číslo, což je pouze zobecnění problému hledání
mediánu.

Při hledání postupujeme tak, že neseřazenou posloupnost projíždíme zleva, dokud nenalezneme
prvek, který je větší (nebo roven) než námi zvolený pivot. Poté projíždíme posloupnost
zprava, dokud nenarazíme na prvek, který je menší (nebo roven) pivotu. V tuto chvíli máme k
dispozici dva prvky, a oba jsou na špatné straně, takže je prohodíme. V procesu zkoumání výměn
pokračujeme tak dlouho, dokud se nestřetneme. Tím je zajištěno, že jsou menší prvky než pivot
umístěny na levé straně a prvky větší než pivot zase na pravé.

Tím však ještě není medián určen, protože pivot byl zvolen (například) náhodně. Můžou
totiž nastat tři případy. V nejideálnější situaci je opravdu pivot mediánem a celý proces hledání
můžeme úspěšně ukončit. Může se však stát, že pivot nebude uprostřed posloupnosti, tedy byl
zvolen nešťastně a není mediánem. V tom případě musíme hledat (např. rekurzivně) medián v
horní, popř. dolní části posloupnosti v závislosti na umístění aktuálního pivota. Jinak řečeno pokud
byl pivot moc malý, upravíme spodní mez posloupnosti. Pokud byl pivot naopak velký,
upravíme horní mez posloupnosti a cel ý postup opakujeme. Pokud je pivot "tak akorát", pak je
naším mediánem.

Očekávaný čas metody FIND je 4n, je nutné však připomenout, že je celé hledání založeno na
Quicksortu, takže může složitost klesnout do kvadratické třídy. Existuje však i lineární řešení viz
následující odstavce.

Algoritmus SELECT
-----------------

SELECT je svým chováním velmi podobný metodě FIND, ale dokáže eliminovat problém se
špatným zvolením pivota. Postupuje se následovně. Nejdříve rozdělíme neseřazenou posloupnost
na pět částí s tím, že jedna nemusí být úplná. Následně najdeme medián každé skupiny. Z
nalezených mediánů najdeme jeden celkový medián. Zde se však nesmíme ukvapit a použít tento
medián jako výsledný. Zatím to totiž byl pouze poměrně spolehlivý odhad vhodného pivota pro
dělení celé posloupnosti.

Opět mohou nastat tři příklady tzn. pivot je rovnou mediánem, pivot je větší, nebo je pivot
menší než medián. Při neshodě pivota s mediánem voláme SELECT rekurzivně do té doby,
než dostaneme požadovaný prvek. Postup hledání se může zdát dost zamotaný a rekurze na
přehlednosti nepřidává, nicméně tento algoritmus má složitost O(n).

# Srovnání zmíněných algoritmů


Hledání mediánu pomocí přímočaré metody vede k seřazení posloupnosti (což nebylo zadáno).
Kromě toho získáme nejlepší složitost O(NlogN), což nemusí být úplně špatné vzhledem k nejhor
ší složitosti Quicksortu O(n^2), ale spotřebujeme více paměti. Oproti tomu algoritmus FIND
nezabere více paměti než je nutné, jeho složitost však může být také kvadratická. Jednoznačně
nejlepší řešení se zdá být metoda vyhledávání SELECT, která nejenže nespotřebuje dodatečnou
paměť, ale navíc si udržuje lineární složitost což je alespoň podle běžně dostupné literatury
nejlepší možné řešení.

# Závěr


Nezáleží-li nám na složitosti, nebo paměťové náročnosti, zvolíme přímočarou metodu hledání
mediánu, která je nejjednodužší na implementaci a pochopení. V opačném případě zvolíme algoritmus
SELECT, který je sice složitý, ale má vynikající výsledky.
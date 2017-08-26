---
id: f9727676-c2eb-4ee0-b86d-8126663f007d
timestamp: 1402842853000
title: Čteme Data Matrix bez čtečky
slug: cteme-data-matrix-bez-ctecky
---
![](https://zlmlcz-media.s3-eu-west-1.amazonaws.com/1fc11f25-abc2-453e-abb4-adb31d0ccf17/datamatrix.png)

Dnešním článkem navazuji na dřívější článek [Čteme QR kódy bez čtečky](cteme-qr-kody-bez-ctecky), ve kterém jsem řešil čtení QR kódu bez použití jakéhokoliv čtecího zařízení. A dnes budu řešit téměř to samé, ale s jiným kódem. Data Matrix není tolik známý, ale myslím si, že patří, hned po QR kódech, mezi nejpoužívanější kódy vůbec. Po tomto článku si jich zajisté začnete všímat více. Naposledy jsem jej viděl na balíčku od jahod... (-: Opět platí, že hlavním cílem je kód přečíst, nikoliv mu úplně porozumět, takže nebudu rozebírat velké detaily.

Levý obrázek je kód o kterém bude celou dobu řeč.

# Trocha nezbytné teorie

![](https://zlmlcz-media.s3-eu-west-1.amazonaws.com/0dd271b6-21ea-4c3c-a447-723b76257b50/datamatrix-key.png)

Tento kód je oproti QR kódu velmi jednoduchý, takže i teorie bude stručná. Veškerá data a korekce chyb je ukryta uvnitř rámu, který je z části plný. To umožňuje čtečkám poznat kde všude je ještě kód a jak je natočen. Z pohledu "ručního" čtení je tato část nezajímavá. Modře jsou zvýrazněny 4 bity, které k ničemu neslouží a zaplňují jen prázdné místo. Ne vždy je tento úkaz vidět. Bohužel tentokrát nemám k dispozici originální specifikaci, takže nevím jakým přesným pravidlům to podléhá.

Nejpodstatnější jsou však červeně zarámované oblasti. Všechny mají takovýto tvar a vždy mají přesně dané umístění. Toto umístění je vlastně dáno první oblastí s číslem 2. Nicméně z hlediska umístění dat je nejdůležitější pátý bit v prvním sloupci od kterého vše startuje. Pokud obrazec přesahuje to timing zón, tedy do nažloutlého rámu, tak pokračuje na druhé straně, tedy dole, nebo na pravé straně.

Data se v kódu čtou podle čísel, tedy podél takové diagonální vlnovky a to až do obrazce s číslem 5. Tento obrazec určuje konec zprávy. V našem případě se tedy jedná o zprávu, která má 4 znaky. A jak už to tak bývá, tak jsou všechny znaky přenášeny v binárním formátu. Zbytek kódu, který není nijak zvýrazněn je klasicky Reed–Solomon korekce chyb. Možná bych o tomto mechanismu také mohl někdy napsat článek. Nicméně napovažuji to za úplně jednoduchá záležitost, takže si to zatím nechám v zásobě.

# Hrajeme si na čtečku


Přečíst zprávu v takto teoreticky rozebraném kódu už je otázka chvilky. Najdříve si vytáhneme všechny potřebné části. V tomto případě je zbytečné řešit pátý člen, jelikož se jedná o poslední ukončovací. Jeho hodnota je 129 v desítkové soustavě. Získané útvary jsou vidět na obrázku níže.

![](https://zlmlcz-media.s3-eu-west-1.amazonaws.com/76028c59-4e5f-42f4-a411-3452844a30d2/last.png)

Čísla určují pozici bitů v binárním čísle. Po přepsání do binární podoby mají znaky následující hodnoty:

```
01100010 01101001 01110000 01101011
```

V desítkové podobě jsou to tedy čísla:

```
98 105 112 107
```

Následně je zapotřebí od těchto čísel odečíst jednotku. Abych řekl pravdu, tak jsem po dlouhém uvažování nedokázal přijít s rozumným vysvětlením proč se to tak dělá. Původně mě napadadlo, že je to kvůli lepšímu rozložení bitů ve výsledném obrazci. Stejně tak jako se u QR kódů bity ještě maskují. To ale nedává smysl. Je to jen posunutí o jedna. Nic víc... Nicméně je zřejmé, že spousta věcí je v takovýchto kódech minimálně na zamyšlení. Ale zpět k poslednímu kroku. Z předchozího článku již všichni vědí, že číslo 97 je v ASCII tabulce hodnota znaku **a**, takže po odečtení jednotky a přepsání do čitelné podoby získáváme řešení:

```
97 104 111 106
a  h   o   j
```

Tento kód je pro čtení bez čtečky daleko snadnější, komplikuje to jen nezvyklé uspořádání bitů. Jaký další kód bych měl dostat pod drobnohled? Snad to půjde. Mám zde totiž rozpracovaný ještě jiný a nemohu k němu sehnat normu pro nahlédnutí, takže jsem se zatím zasekl... (-:
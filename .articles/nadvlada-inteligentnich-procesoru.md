---
id: 3979a0f0-f8ed-45c3-8311-d48e563a49b1
timestamp: 1416264254000
title: Nadvláda inteligentních procesorů
slug: nadvlada-inteligentnich-procesoru
---
Pár dní zpátky jsem tweetoval o tom, nad čím právě teď trávím asi nejvíce času. Cílem celého mého snažení je dostat data z procesoru, který obsluhuje různé periferie na základní desce až do webového prohlížeče a zpět - vše v reálném čase. Právě dnes jsem dosáhl prvního milníku, tedy dostal jsem nějaká skutečně smysluplná data do prohlížeče a rád bych zde nastínil jak jsem postupoval a jaké technologie jsem volil. Již dříve se totiž ukázalo, že některé reakce na mé články jsou skutečně konstruktivní a tedy i velmi přínosné... (-:

<blockquote class="twitter-tweet" lang="en"><p>Procesor ➡Ethernet ➡NodeJS ➡Redis ➡ExpressJS ➡Socket.IO ➡Browser ✅ <a href="http://t.co/MAIlmMZEL9">pic.twitter.com/MAIlmMZEL9</a></p>&mdash; Martin Zlámal (@mrtnzlml) <a href="https://twitter.com/mrtnzlml/status/531104236571230208">November 8, 2014</a></blockquote>
<script async src="//platform.twitter.com/widgets.js" charset="utf-8"></script>

# Několik slepých uliček

Původně byl celý nápad úplně jiný a rozhodně ne real-time. A stejně jako je teď velký trend stavět vše pomocí *Raspberry Pi* nebo pomocí *Arduino*, tak i já jsem tak chtěl učinit. Důvod je jednoduchý. Napsat se s pomocí těchto desek dá všelicos velmi rychle a nestojí to moc námahy. Navíc jsem chtěl více času věnovat webové aplikaci, než nějakému mikročipu. Postupem času a po několika vážných rozhovorech jsem začal přemýšlet nad real-time přenosem informace v obou směrech. Takže bylo zapotřebí najít vhodnou desku. Jenže na real-time už jsem si jen s *Raspberry* nevěřil a proto jsem hledal nějakou lepší desku na kterou bych nahodil nějaký velmi lehký Linux a NodeJS. Po dalších vážných rozhovorech jsem však i z této cesty ustoupil a zvolil jsem něco, co jsem ze začátku vůbec neuvažoval. Chtěl jsem zvolit desky od ST z řady [Nucleo](www.st.com/stm32nucleo), ale tyto desky nemají ethernet a tak bylo lehčí vypůjčit si [eval-boardy](www.st.com/stm3220g-eval). Tyto desky mají stejné procesory, ale více periferií okamžitě k dispozici. Jenže to znamená, že musím opustit všechno hraní si a hluboce se ponořit do **C**čka...

# Správná ulička?

Desky a procesory jsou tedy jasné. Co dál? Nezbývá než stanovit, jak se bude informace přenášet až do prohlížeče. Zde je zřejmě jasná věc to, že mezi procesorem a prohlížečem musí být nějaký mezičlen. Ten u mě tvoří [NodeJS](http://nodejs.org/) server v kombinaci s [Redis](http://redis.io/) databází zhruba tak, jak je znázorněno na [ASCII artu](http://cs.wikipedia.org/wiki/ASCII_art) níže:

```
------------      ----------      -----------
| Procesor | ---> | NodeJS | ---> | Browser |
------------      ----------      -----------
        |             ^
        |             |
        |         ---------
        --------> | Redis |
                  ---------
```

Jak celý přenos v současné chvíli probíhá? Procesor je teď aktivním prvkem, tzn. sbírá nějaká potřebná data (např. pomocí ADC snímá polohu natočení potenciometru) a pomocí UDP datagramů je odesílá na jasně danou IP adresu NodeJS serveru, kde se informace z datagramu uloží do dané struktury v Redis databázi. Tyto datagramy se skládají z klasické UDP hlavičky a datové části. Ta je v [RESP](http://redis.io/topics/protocol) formátu, takže teoreticky bude někdy později možné NodeJS server úplně přeskočit a data ukládat přímo z procesoru do databáze jak je na diagramu naznačeno. Server však nikdy nepůjde úplně odstranit, protože na NodeJS serveru běží v této chvíli UDP server, ale také tam běží webový server ([ExpressJS](http://expressjs.com/)), který mi umožňuje rovnou vytvořit webovou stránku a s pomocí [Socket.IO](http://socket.io/) si mohu otevřít websocket a z databáze opět informace odesílat bleskovou rychlostí do prohlížeče, kde je mohu javascriptem nějak dále zpracovat.

# K čemu to celé je?

Nevím. (-: Tento článek jsem napsal proto, abych si utřídil myšlenky a získal zpětnou vazbu. Celý projekt měl však původně být pro tzv. inteligentní domy, kdy by stačilo dům pouze zasíťovat a vše ostatní by již bylo vlastně hotovo (přes ethernet lze i napájet). Výhodné je to v tom, že můžete kdykoliv jakýkoliv prvek domácnosti odpojit, dát ho na jiné místo (v rámci IP rozsahu) a vše by stále fungovalo. Jenže jak často přesouváte vypínače (termostaty, světla, senzory)? Proto se chci spíše zaměřit na objekty kde se tyto věci často řeší, proto vidím správný směr spíše v (relativně) často se měnících kancelářských budovách, nebo tam kde je potřeba například rychle připojit nějaké senzory a sledovat je online. Každopádně věřím, že díky svým možnostem tento systém natrhne zadek i současným systémům pro ovládání "inteligentních" domů využívajících PLC-like systémy.

Budoucnost je zatím celkem jasná. Teď chci naučit systém komunikovat i v opačném směru a následně jej budu pilovat, čímž završím první etapu práce. Cest pro rozšiřování je však ještě spoustu. Jednak chci systém naučit komunikovat bezdrátově, pak také IPv6, šifrovanou komunikaci a v neposlední řadě chci vyrobit nějaké senzory a akční členy pro reálné použití a zprovoznit funkční ukázku pro prezentování celé této srandy.

Tak co, máte dostatek odvahy šoupnout si takový systém do baráku? Upozorňuji, že to není žádná Arduino-like stavebnice, takže to je spolehlivé... (-:
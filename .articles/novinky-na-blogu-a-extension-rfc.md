---
id: d0b9d1c4-61c7-49d1-9f10-e645525a5726
timestamp: 1405252851000
title: Novinky na blogu a extension RFC
slug: novinky-na-blogu-a-extension-rfc
---
Kdo pravidelně sleduje [můj twitter](https://twitter.com/mrtnzlml), tak už to ví. A je to skvělé! Včera jsem totiž [mergnul](https://github.com/mrtnzlml/zlml.cz/commit/b21775df522271e81302d2987ff44d5285b245eb) důležitou část tohoto blogu a tím vydal verzi 1.1 snad stable... (-: Jedná se o celkem významný krok, ale stále je to nic oproti tomu co mám v plánu. V dnešním článku bych se rád podělil o většinu zajímavých novinek a v druhé části bych rád požádat o pomoc s rozvinutím jednoho zajímavého nápadu.

# Novinky v release 1.1

A hned první novinka je ta nejzajímavější. Zastávám názor, že by si měl programátor za svým kódem stát a to v každé situaci. Proto jsem se rozhodl, že pustím kohokoliv do své administrace. Ostatně proč ne. Je to výzva. Rád bych však napřed požádal kohokoliv kdo najde nějakou chybu, aby mi to dal vědět na základě nepsané programátorské etikety a nesnažil se celý blog hned zničit. Zálohu mám... :-) Administraci najdete na adrese zlml.cz/admin a přístupové údaje jsou `demo / demo`. Enjoy.

Ačkoliv je první představená novinka asi nejzajímavější pro kohokoliv jiného, pro mě osobně je nejzajímavější nový systém vkládání obrázků. To je něco co mi na blogu dlouho chybělo. Představoval jsem si to tak, že bych obrázky jednoduše a rychle nahrál a stejně jednoduše bych je vložil do aktuálně psaného článku. A přesně to teď můžu udělat. Použil jsem [fineuploader](http://fineuploader.com/), protože je to skvělý program. Můžu AJAXově nahrávat souběžně několik obrázků s velikostí klidně až za PHP limitem `upload_max_filesize` a nebo navázat na přerušené nahrávání. To vše vlastně díky chunk uploadu. Umí toto váš blog? Zkuste si na sdíleném hostingu nahrát soubor o velikosti třeba 50MB...

Toto jsou dvě nejdůležitější změny, na kterých jsem v desetinkové verzi pracoval. Správa uživatelů a cool image uploader. Dále jsem udělal opět několik designových změn a fixů, ale už se nejedná o nic tak důležitého aby to stálo za řeč.

# Další kroky a žádost o pomoc

Svůj blog mám rád. A mám ho rád až tak, že bych byl rád, aby ho časem používal i někdo jiný. To ještě nějakou chvíli potrvá, ale už nějaký čas mi leží v hlavě nápad, jak se k tomuto požadavku přiblížit. Rád bych totiž blog přepsal do Nette extensions. Včera jsem popré [nakousl](https://github.com/mrtnzlml/zlml.cz/commit/1ffc33bc5dddbadfc1b6ce7d30dccb09938800cb) svojí myšlenku. Princip je jednoduchý. Přepíšu například funkcionalitu obrázků (nebo čehokoliv jiného) do extension a když bude někdo budovat nový blog, tak si jen v konfigu zvolí jaké moduly chce (to se dá snadno generovat):

```neon
extensions:
	- ImageExtension
    - SearchExtension
    - ...
```

Samotné extension má potom za úkol se samo spustit (`afterCompile`) a přidat nějaké funkcionality do stacku (v metodě `initialize`). V prvním nakousnutí například přidávám položku do menu a skutečně to funguje tak jak píšu. Jakmile nějakou funkci nechci, prostě ji z konfigu zruším. Výhodné na tom je to, že můžu prošpikovat blog místy, kam lze nové feature zaregistrovat a dají se velmi jednoduše provázat přes composer. **Ale.** Má to háček.

Když jsem si s touto myšlenkou hrál jen v hlavě, tak to bylo super, protože vše fungovalo perfektně. Bohužel jsem narazil na to, že nevím jak pěkně vyřešit šablony a routování na ně. Šablony jsou totiž jedna z dalších věcí které bych zde chtěl vyřešit. Bylo by fajn, kdyby mohl mít každý možnost si třeba šablony podědit a změnit si kompletně celý design. To zase takový problém není. Ale jak vyřešit to, když chci v extension např. přidat novou stránku? Kam umístit šablonu a jak ji naroutovat? Nějak jsem to dneska v noci už nedokázal vymyslet. Máte někdo prosím nějaký zajímavý nápad? Díky...

No a naposledy už snad jenom: [Give me a star, please](https://github.com/mrtnzlml/zlml.cz)...
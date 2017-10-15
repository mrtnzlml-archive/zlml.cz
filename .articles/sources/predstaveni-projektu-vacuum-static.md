---
timestamp: 1390929034000
title: Představení projektu Vacuum - STATIC
slug: predstaveni-projektu-vacuum-static
---
Vzhledem k tomu, že vzrostl zájem o Vacuum projekty, rozhodl jsem se zde uveřejnit postup jak pracovat s projektem **Vacuum - STATIC** (https://bitbucket.org/mrtnzlml/vacuum-static). Věřím, že je daleko lepší projekt ukázat a lehce popsat, než popsat a lehce ukázat, jak si Nette komunita občas myslí...

# Co to vlastně je

Vacuum - STATIC je projekt, který vznikl z úplně základního Nette skeletonu, který jsem používal pro jednoduché statické firemní prezentace jako je například http://www.businessservice.cz/. Postupem času jsem dodával vylepšení a když už tento projekt dosáhl svého maxima, rozhodl jsem se do něj napsat jednoduchou administraci. V současné době tedy Vacuum - STATIC vlastně vůbec není statická prezentace. Obsahuje vestavenou SQLite databázi, díky které není potřeba nějaké MySQL databáze. Web prostě funguje zdánlivě bez databáze. Toto je velmi zásadní. Vacuum - STATIC pravděpodobně nikdy nebude mít externí databázi, takže se bude stále tvářit jako jednoduchá webová prezentace, která má však navíc jednoduchou administraci.

# Stažení, instalace, spuštění

Celý projekt se dá stáhnout různě, asi nejjednodušší je využít funkcionalit GITu:

```
>> git clone https://mrtnzlml@bitbucket.org/mrtnzlml/vacuum-static.git folder
Cloning into 'folder'...
remote: Counting objects: 433, done.
remote: Compressing objects: 100% (401/401), done.
remote: Total 433 (delta 201), reused 0 (delta 0)
Receiving objects:  92% (399/433), 636.00 KiB | 192 KiB/s
Receiving objects: 100% (433/433), 664.47 KiB | 192 KiB/s, done.
Resolving deltas: 100% (201/201), done.
```

Tím vytvoříte složku `folder`, která bude obsahovat aktuální verzi projektu Vacuum - STATIC. Pokud v tuto chvíli projekt sputíte, vrátí chybu, že nemůže najít soubor `autoload.php`. Je to proto, že projekt ještě neobsahuje žádné knihovny (například Nette). Ty totiž nemá smysl udržovat v repozitáři. Veškeré potřebné knihovny lze doinstalovat jednoduše pomocí Composeru:

```
>> composer update
Loading composer repositories with package information
Updating dependencies (including require-dev)
  - Installing nette/tester (dev-master a60c379)
    Cloning a60c379836617422c8df9d9846fea4efa2ca9d1d

  - Installing nette/nette (dev-master a748c3d)
    Cloning a748c3d344767ed1f0cc9ee40019f6a6f81afa97

  - Installing janmarek/webloader (dev-master 3d44d30)
    Cloning 3d44d306d59591dc94f6fdcb98f55c0990d98326

  - Installing texy/texy (dev-release-2.x 79d0e15)
    Cloning 79d0e1517363ab32edf2db8ec515e3dc84f50f0a

nette/nette suggests installing ext-fileinfo (*)
janmarek/webloader suggests installing leafo/lessphp (Lessphp is a composer for LESS written in PHP.)
Writing lock file
Generating autoload files
```

V tuto chvíli je projekt připraven k použití. V některých systémech však bude potřeba ještě nastavit práva k zápisu složkám `temp` a `log`. Dokonce není potřeba ani nějakého XAMPP serveru. Stačí v té samé složce využít integrovaného PHP serveru v příkazové řádce:

```
php -S localhost:8888 -t www
```

Funkční Vacuum - STATIC pak naleznete na adrese http://localhost:8888/. Trapně jednoduché a překvapivě funkční. (-: Vzhledem k tomu, že již vidíte funkční stránku, můžete se přihlásit do administrace (link v patičce). Přihlašovací údaje jsou *demo*/*demo*.

# Update projektu

Na tomto projektu stále pracuji a čas od času v něm něco doplním nebo upravím. Vzhledem k tomu, že doporučuji použít ke stažení GIT, je update projektu velmi jednoduchý. Stejně jako jsem nedávno zapomněl přidat nahrát dva soubory:

```
>> git pull
Updating ead4a56..2439d5f
Fast-forward
 www/js/codemirror.js | 5516 ++++++++++++++++++++++++++++++++++++++++++++++++++
 www/js/xml.js        |  338 ++++
 2 files changed, 5854 insertions(+)
 create mode 100644 www/js/codemirror.js
 create mode 100644 www/js/xml.js
```

To je asi tak vše co se k tomu dá teď napsat. Nic na tom není, jen je třeba vědět jak na to. Přeji hodně úspěchů při používání tohoto projektu ať už je to ke studijním účelům, nebo k reálné webové prezentaci. Zpětnou vazbu samozřejmě rád uvítám.
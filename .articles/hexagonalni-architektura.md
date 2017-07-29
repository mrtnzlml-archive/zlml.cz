Struktura webových aplikací je něco, co se neustále mění a stejně s programátorem i zdokonaluje. Před více než rokem jsem se o jedné z možných struktur PHP aplikace [trošku rozepsal](jeste-lepsi-struktura-nette-aplikace). A teď si ukážeme další alternativu, která je [k proklikání zde](https://github.com/adeira/connector/tree/2169296c8da4a50bf4f928e94e756f3b23afea24). Nejedná se o nic převratného. Tento přístup mě však v poslední době hodně baví a dá se na něm naučit zase něco nového. Jedná se vlastně o tento adresářový strom (zjednodušeně):

```
.
├── bin/            ...  pomocné SH skripty
├── config/         ...  globální konfigurace aplikace
├── migrations/     ...  dopředné DB migrace
├── src/
├── tests/          ...  veškeré testy
├── var/            ...  logy a temp
├── vendor/         ...  balíčky třetích stran
├── www/            ...  veřejná složka
├── bootstrap.php   ...  vytváření DI kontejneru
├── composer.json   ...  definice závislostí třetích stran
├── composer.lock
├── LICENSE.txt
└── README.md
```

Na první pohled je asi vše jasné. Struktura je téměř identická s tím, co je běžné u začínajících Nette projektů (Nette zde však není vůbec důležité). Za povšimnutí stojí pouze to, že `bootstrap.php` a globální konfigurace jsou na úrovni kořenového adresáře, takže neexistuje žádná složka `app`. Zůstává tedy otázka - kde jsou veškeré kódy a jak je aplikace členěna?

# Kontexty

Veškerá zábava je právě ve složce `src` (jak se asi dalo čekat):

```
src/
├── Authentication/
├── Common/
├── Devices/
├── Endpoints/
└── Routing/
```

Osobně mám pořád problém s pojmenováváním věcí, takže mám pořád takový pocit, že jednotlivé kontexty nejsou dobře zaškatulkované. To ale není zase až tak důležité. Důležitá je následující myšlenka: každá tato podsložka (kontext) se stará o úplně všechno. Od konfigurací, přes presentery až po business logiku. Uvnitř kontextu nejsou pouze balíčky třetích stran a testy (ty jsou v `tests`, ale mají úplně stejnou adresářovou strukturu - ne nutně soubory).

Je třeba trošku se pozastavit nad konfigurací. Každý balíček si s stebou nese vlastní konfigurační soubory, které potřebuje k životu. Ty většinou velmi úzce souvisejí např. s konfigurací DI kontejneru. Ale pouze pro potřeby daného kontextu! Pokud je třeba tento balíček konfigurovat na globální úrovni celé aplikace, pak je tento balíček zaregistrován jako DI rozšíření. Tato úprava myšlení nad konfigurací je možná díky balíčku [adeira/compiler-extension](https://github.com/adeira/compiler-extension). Doporučuji alespoň přečíst a doufat, že to bude fungovat i v Nette 3 ([Proč by nemohlo?](https://github.com/nette/di/issues/143)).

Samostatná interní konfigurace kontextu je asi to nejdůležitější pro pokračování (může být také pěkně [dlouhá a komplikovaná](https://github.com/adeira/connector/blob/2169296c8da4a50bf4f928e94e756f3b23afea24/src/Devices/Infrastructure/DI/Nette/config.neon)). Co obsahuje takový kontext (balíček)?

```
src/Devices/
├── Application/
│   ├── Exceptions/
│   └── Service/
├── DomainModel/
│   └── WeatherStation/
└── Infrastructure/
    ├── Delivery/
    ├── DI/
    ├── DomainModel/
    └── Persistence/
```

To už je o něco zajímavější a také řádově komplikovanější na pochopení. Dále se totiž dělí aplikace na tři důležité části, kde každá má jiný význam a každá si tedy zaslouží krátký komentář. Začněme pěkně uprostřed.

# Doménová vrstva

V doménové vrstvě je pouze obyčejné PHP. Myšlenka je taková, že se zde budu soustředit pouze na návrh modelu v čistém PHP a nebudu to nijak komplikovat zanášením jakýchkoliv frameworků či jiných externích knihoven:

```
src/Devices/DomainModel/
├── WeatherStation
│   ├── IAllWeatherStationRecords.php
│   ├── IFileLoader.php
│   ├── WeatherStationId.php
│   ├── WeatherStation.php
├── Humidity.php
├── PhysicalQuantities.php
├── Pressure.php
├── Temperature.php
└── Wind.php
```

Nikde zde nenajdete v kódu slovo Nette, Symfony nebo třeba Doctrine (_ve skutečnosti to tam je, ale to je chyba_). Jsou to prostě úplně obyčejné objekty, které mají za úkol řešit nějaký konkrétní problém onoho konkrétního kontextu a nic víc. Díky tomu je psaní testů na tuto část aplikace naprosto triviální. Když někde existuje nějaká závislost, tak je zprostředkována pomocí rozhraní, takže vyměnit implementaci v testech je díky tomuto striktnímu rozdělení opravdu hračka.

Takto by však aplikace sama o sobě nefungovala. Je potřeba mít zde vazbu na konkrétní implementaci, na konkrétní framework či knihovnu. Od toho slouží další vrstva.

# Infrastrukturní vrstva

Zde není nic jiného, než implementace (implementační detaily). Většinou se jedná o třídy, které nemají žádnou zvláštní nebo složitou logiku. Slouží pouze jako napojení na framework a jako implementace rozhraní z domménové vrstvy. To se silně projeví ve struktuře:

```
src/Devices/Infrastructure/
├── Delivery/
│   ├── API/
│   │   └── GraphQL/
│   ├── Console/
│   │   └── Symfony/
│   └── Http/
│       └── Nette/
├── DI/
│   └── Nette/
│       ├── config.neon
│       └── Extension.php
├── DomainModel/
│   └── WeatherStation/
│       ├── Doctrine/
│       └── Series/
└── Persistence/
    ├── Doctrine/
    │   ├── Mapping/
    │   └── DoctrineAllWeatherStations.php
    └── InMemory/
        └── InMemoryAllWeatherStations.php
```

Trošku se to komplike, že? Po chvilce studování to však dává celé smysl. Tak třeba `Delivery` - je potřeba doručit nějakou šablonu prostřednictvím presenteru, poslat JSON nebo komunikovat s CLI. Proto je zde vždy vazba na konkrétní technologii (o tom ostatně celá tato vrstva je). Podobně napojení na DI. Může se zdát, že to vždy bude Nette, ale pokud bych podobným stylem vydával i Composer knihovny, tak je jednoduché dodělat podporu i pro další frameworky - kód je na to připraven.

Asi nejzajímavější je potom složka `Persistence`, která řeší ukládání dat _někam_. V mém případě je to primárně Doctrine, takže se zde musím hodně zasnažit, abych dokázal dříve vytvořené objekty uložit do databáze a přitom o tom žádný z těchto objektů netušil (občas dřina, ale všechno jde). Ale implementací může být více - třeba v obyčejné paměti PHP. Což je super strategie pro testování. V testech (pokud to není smyslem toho testu) nepotřebuji pracovat přímo s pomalou databází, takže nahradit její implementaci za ukládání do paměti bude super rychlé.

Zbývá poslední vrstva.

# Aplikační vrstva

Mít takto myšlenkově oddělené závislosti jednotlivých částí aplikace má spoustu výhod. Důležité je zeptat se: jak budeme tento model ovládat? Odpovědí je právě aplikační vrstva. Ta má za úkol pouze jednu věc. Zpřístupnit doménovou vrstvu širokému okolí. Pokud tedy presenter chce komunikovat s doménovou vrstvou, musí prostřednictvím aplikační vrstvy. Proč musí? Nestačilo by rovnou používat nějaký interface pro repozitář? Stačilo, ale to není dobrý nápad!

Rovnou pracovat s repozitáři není rozumné, protože ty maximálně tahají data z úložiště. To je jejich zodpovědnost, ale co třeba oprávnění a transakce? O to se právě stará aplikační vrstva. Jako příklad budeme chtít vytvořit záznam pro novou meteostanici. Zde konkrétně využívám přístup CQS (Command-Query Separation). Zde existují dva způsoby jak komunikovat. Pomocí dotazů, kdy každý dotaz vrací požadovanou informaci, ale **nemodifikuje data** a pomocí příkazů, které data modifikují, ale **nevrací žádná data zpět**.

Takový příklaz by se mohl jmenovat `CreateWeatherStation` a jedná se pouze o obyčejný DTO objekt, který nemá žádnou logiku a měl by být tak jednoduchý, že jej není třeba testovat (pokud nechcete). Je to fakt jenom přepravka na data. Tento příkaz má svého parťáka, který jej umí zpracovat. Příkaz odesílám pomocí sběrnice třeba z API:

```php
$this->commandBus->dispatch(new CreateWeatherStation(
    WeatherStationId::create(), // ID nové stanice
    $args['name'], // název stanice
    $context->userId() // ID uživatele, který vytváří stanici
));
```

Tato sběrnice jednak doručí příkaz na to správné místo pro zpracování, ale také obstará databázové transakce, prokud je to nutné. Tento příkaz se zpracuje v `handleru`, který implementuje metodu `__invoke` (nebo cokoliv jiného co je `callable`):

```php
public function __invoke(CreateWeatherStation $aCommand)
{
    $owner = $this->ownerService->existingOwner($aCommand->userId());

    $this->weatherStationRepository->add(new WeatherStation(
        $aCommand->stationId(),
        $owner,
        $aCommand->name(),
        new \DateTimeImmutable('now')
    ));
}
```

Kouzlo je v tom, že je zde ukryta i kontrola ověření, protože na prvním řádku se snažím získat z uživatele vlastníka (jinak exception) a samotná meteostanice také chce v konstruktoru vlastníka (nikoliv jen uživatele). Kontrolu tedy nejde zapomenout. `weatherStationRepository` je zde opět jen interface. Jak je vidět, tak je zde hodně věcí na které se nesmí zapomenout a proto je rozumné přistupovat k doménovému modelu pouze přes tuto aplikační vrstvu.

Na závěr nutno jednu drobnou poznámku. Tento přístup (hledejte pod názvem hexagonální architektura) mě moc baví a pořád je co se zde učit nového. Pokud však patříte do kategorie "líný programátor", pak pro vás tento přístup určitě není vhodný. Je to totiž spousta práce, která se může zdát jako zbytečná. Klidně se to vše nechá dělat jednoduše v jedné vrstvě. Hexagonální architektura však nabízí elegantní řešení problémů, které občas ve vývoji nastanou a myslím si, že hledí hodně vpřed. Doporučuji tedy alespoň jednou vyzkoušet a pokud nic jiného, tak si z toho alespoň odnést některé myšlenky, které člověka posunou zase o kousek dál... :)
Na 85. Poslední sobotě v Praze jsem měl workshop a přednášku o GraphQL. Na konci přednášky padl velmi dobrý dotaz ohledně toho, jestli náhodou netrpí GraphQL v určitých situacích N+1 problémem. Načež jsem odvětil, že to není problém GraphQL, ale že ano. Teď je tedy ten správný čas podívat se na tuto situaci konkrétně a pokusit se ji vyřešit. Skvělé je, že [knihovna, kterou používám](http://webonyx.github.io/graphql-php/) na sebe částečně převzala řešení tohoto nešvaru. Ale o tom až za chvíli...

# N+1 problém

S [N+1 problémem](https://secure.phabricator.com/book/phabcontrib/article/n_plus_one/) se setkal každý, kdo naprogramoval alespoň blog s komentáři. Problém nastává pokud se z databáze netahají všechna potřebná data s předstihem. Například pokud u blogu vytáhneme pouze články a až později budeme iterovat články a tahat k nim komentáře, pak máme N+1 problém. Stane se totiž, že položíme na databázi stejný počet dotazů, jako máme článků.

Řešit se to dá dvojím způsobem. Můžeme použít databázový `JOIN` a v jednom dotázu si vytáhnout vše potřebné (články a kometáře) s tím, že již víme co potřebujeme. A nebo použijeme `IN` klauzuli a druhým dotazem se doptáme na vše potřebné (zbývající komentáře). Tento přístup se pro GraphQL hodí více.

Hezký příklad tohoto problému je vidět na tomto GraphQL dotazu:

```
{
  query_1: allWeatherStations {
    edges {
      node {
        ...RecordsFragment
      }
    }
  }
  query_2: allWeatherStations {
    weatherStations {
      ...RecordsFragment
    }
  }
}

fragment RecordsFragment on WeatherStation {
  records {
    id
  }
}
```

Toto by se dalo nazvat M*N+M problémem... :) Kdybych totiž posílal jen první dotaz (dej mi meteostanice a ke každé stanici všechny záznamy), vznikl by dříve popisovaný problém. Jen jsou zde místo článků meteostanice a místo komentářů jednotlivé záznamy stanic. Jenže v GraphQL lze skutečně složit graf a tak se můžu ptát velmi složitě a zamotaně. Dokonce se můžu ptát pořád dokolečka:

```
{
  articles { # get all articles
    authors { # get all authors of the articles
      theirArticles { # get all articles they wrote
        authors { # get authors of those articles
          theirArticles {
            ...
          }
        }
      }
    }
  }
}
```

Neříkám, že to dává smysl, ale pokud je API dostatečně kompikované, tak se zde můžou objevit cyklické cesty.

# Řešení

Podívejme se, jak se tahají jednotlivé záznamy pro konkrétní meteostanici:

```php
$field->setResolveFunction(function (WeatherStation $ws, $args, UserId $userId) {
    return $this->allWsRecords->execute($userId, $ws->id());
});
```

Slovy řečeno: v callbacku přijde meteostanice a my se zeptáme nějaké modelové třídy na všechny záznamy podle ID meteostanice. To je přesně to místo, kde vzniká N+1 problém. V knihovně [webonyx/graphql-php](https://github.com/webonyx/graphql-php) je od verze `v0.9.0` k dispozici objekt `GraphQL\Deferred`, který perfektně poslouží k optimalizaci:

```php
$field->setResolveFunction(function (WeatherStation $ws, $args, UserId $userId) {
    $this->allWsRecords->buffer($ws->id());

    return new \GraphQL\Deferred(function() use ($userId, $ws) {
        return $this->allWsRecords->execute($userId, $ws->id());
    });
});
```

Vtip je v tom, že při řešení dotazu prochází knihovna `webonyx/graphql-php` celý graf a zjišťuje potřebné hodnoty. Ve chvíli kdy dojde k našemu uzlu/listu, tak si jen poznamenáme (buffer) jaké ID bylo práve vyžadováno a vrátíme onen `Deferred` objekt. Až celý proces dojde na samotný konec, tak se knihovna zeptá ještě na ty odložené objekty. V tu chvíli ale již víme jaké všechny ID jsou potřeba a můžeme je získat jedním dotazem a postupně vracet:

```php
if(empty($this->weatherStationIdsBuffer)) {
	return $this->wsrr->ofWeatherStationId($weatherStationId);
} else {
	static $result = NULL; //memoization
	if ($result === NULL) {
		$result = $this->wsrr->ofAllWeatherStationIds($this->weatherStationIdsBuffer);
	}
	return $result[$weatherStationId->id()];
}
```

Tedy pokud není nic v bufferu, prostě se na to jedno ID zeptáme do databáze. Zde není co optimalizovat. V opačném případě však pošleme dotaz s `IN` a zeptáme se na všechny ID, které jsou v bufferu. Ty budeme chvíli držet v lokální cache a postupně ven servírovat jednotlivé záznamy (bez dalších dotazů do databáze).

Výsledek je následující (předtím):

```sql
SELECT t0.* FROM user_accounts t0 WHERE t0.id = ?

SELECT w0_.* FROM weather_stations w0_ WHERE w0_.owner_uuid = ?
SELECT w0_.* FROM weather_stations_records w0_ WHERE w0_.weather_station_id = ?
SELECT w0_.* FROM weather_stations_records w0_ WHERE w0_.weather_station_id = ?
SELECT w0_.* FROM weather_stations_records w0_ WHERE w0_.weather_station_id = ?
SELECT w0_.* FROM weather_stations_records w0_ WHERE w0_.weather_station_id = ?
SELECT w0_.* FROM weather_stations_records w0_ WHERE w0_.weather_station_id = ?
SELECT w0_.* FROM weather_stations_records w0_ WHERE w0_.weather_station_id = ?

SELECT w0_.* FROM weather_stations w0_ WHERE w0_.owner_uuid = ?
SELECT w0_.* FROM weather_stations_records w0_ WHERE w0_.weather_station_id = ?
SELECT w0_.* FROM weather_stations_records w0_ WHERE w0_.weather_station_id = ?
SELECT w0_.* FROM weather_stations_records w0_ WHERE w0_.weather_station_id = ?
SELECT w0_.* FROM weather_stations_records w0_ WHERE w0_.weather_station_id = ?
SELECT w0_.* FROM weather_stations_records w0_ WHERE w0_.weather_station_id = ?
SELECT w0_.* FROM weather_stations_records w0_ WHERE w0_.weather_station_id = ?
```

A potom:

```sql
SELECT t0.* FROM user_accounts t0 WHERE t0.id = ?
SELECT w0_.* FROM weather_stations w0_ WHERE w0_.owner_uuid = ?
SELECT w0_.* FROM weather_stations w0_ WHERE w0_.owner_uuid = ?
SELECT w0_.* FROM weather_stations_records w0_ WHERE w0_.weather_station_id IN (
    'df40acdd-5222-4f89-a693-999f2d3f3eb6',
    '6f5fb680-f5e3-4d8c-b7e1-27205b848657',
    '14837156-c662-4e8c-b527-2227506c2bf7',
    '2965494d-d13f-4415-8535-b910ac29326a',
    '662c0434-9eca-4241-9462-ce85d279fd6b',
    '36eed5a8-08fd-48db-8153-67355d092201'
)
```

To je obrovské zlepšení. Zejména když si uvědomíte, že k optimalizaci N+1 dotazu nedochází pouze naivně v rámci jedné cesty v grafu, ale v rámci úplně celého grafu. Pokud chcete vidět konkrétní změnu v rámci projektu z workshopu, tak je vidět v commitu [ed8b43](https://github.com/adeira/connector/commit/ed8b43257b778b6b2d4adb1b92baae18daf36905).

Toliko má odpoveď... :)
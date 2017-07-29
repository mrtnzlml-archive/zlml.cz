Na konci minulého roku jsem začal něco jako virtuální seriál o React vs. PHP aplikaci. Včera jsem na Poslední sobotě byl upozorněn na to, že už asi nepokračuju. To není pravda - pouze jsem je přestal číslovat... :) Po [GraphQL](2-graphql), vyřešení [N+1 problému](reseni-n-1-problemu-v-graphql), [architektuře serverové části](hexagonalni-architektura) a [omezení CSS kontextu](jak-na-lokalni-css-pro-react) v React komponentách je čas podívat se podrobněji na komunikaci se serverem.

Ve výsledku jsem hodně rád, že jsem to tak oddaloval, protože jsem to asi tak třikrát celé předělával a konečně mám radost z toho jak to vypadá. Veškeré kódy týkající se [frontendu](https://github.com/adeira/connector-frontend) i [backendu](https://github.com/adeira/connector) jsou jako vždy k dispozici online pod MIT.

Klientské komponenty
====================
Až do nedávné chvíle jsem na straně webového prohlížeče používal pro komunikaci se serverem knihovnu Apollo. Interně Apollo využívá Redux store a já začal hodně vážně zvažovat, že začnu Redux store využívat mnohem více. V tu chvíli už nedávalo moc velký smysl používat Apollo a přišlo mi zajímavější starat se o Redux store sám.

Dříve jsem Apollo používal tak, že existovala vždy nějaká nadřazená komponenta, která se dotazovala serveru a předávala data jiné komponentě. Takže jsem měl komponenty, kterým se říká kontejnery (pouze tahají data) a předávají data pro vykreslení tzv. prezentačním komponentám (pouze vykreslují, ale netahají data).

Myšlenka kontejnerů a prezentačních komponent pořád zůstává. Rozdíl je teď v tom, že místo toho, aby kontejner získával data prostřednictvím Apolla, tak je připojen k Redux úložišti a v okamžiku připojení komponenty do DOMu se spustí Redux akce pro načtení dat:

```javascript
export const AllCamerasContainer = class extends React.Component {

  componentWillMount() {
    this.props.dispatch(loadAllCameras());
  }

  render = () => { /* ... */ }

};

export default connect()(AllCamerasContainer);
```

Funkce `this.props.dispatch` je k dispozici díky nadřazené komponentě, která se vytváří pripojením k Redux úložišti pomocí `connect()`.

Redux funguje tak, že se pomocí funkce `dispatch` vyvolá nějaká akce/příkaz (zde načtení všech kamer), ta prolítne reducerama což jsou úplně obyčejné funkce, které umí měnit podobu stavu Redux úložiště a následně se tento nový stav uloží a komponenta se automaticky překreslí. Důležité je, že Redux si vlastně drží stav všech komponent u sebe v jednom velkém globálním úložišti a akce resp. reducery slouží pro alespoň trošku rozumné ovládání jeho obsahu. Zároveň Redux úložiště nemá z hlediska kódu **nic společného s Reactem**. Redux je ke komponentě nějak napojen, ale může fungovat úplně bez Reactu.

Funkce `loadAllCameras` slouží pro vytvoření objektu reprezentující nějakou akci. Ta se potom spouští pomocí `dispatch`. Akce je obyčejný JS objekt, který si nese informaci o typu akce + nějaká další dodatečná data. Pokud však chci data teprve načíst, je nutné vytvářet místo objektu funkci, která tak učiní:

```javascript
// relies on Redux Thunk middleware
export const loadAllCameras = () => {
  return dispatch => {

    dispatch({ // spuštění akce (což je jen obyčejný objekt) uvnitř jiné akce
      type: ALL_CAMERAS_LOADING,
    });

    fetch(allCamerasQuery).then(({data}) => { // funkce pro získání dat (kvůli tomu je nutný Thunk)

      dispatch({ // opět jen obyčejná akce spuštěná uvnitř jiné akce
        type: ALL_CAMERAS_LOAD_SUCCESS,
        cameras: data.allCameras,
      });

    });
  }
};
```

Zde začíná být vidět důvod, proč jsem opustil Apollo. Díky tomu, že mám teď v ruce veškeré akce a reducery, tak mohu pohodlně spouštět a řetězit akce jak se mi zachce. Až Redux spustí tuto akci (resp. [Redux Thunk](https://github.com/gaearon/redux-thunk)), tak si jen najde ten správný reducer (podle typu `ALL_CAMERAS_LOADING` resp. `ALL_CAMERAS_LOAD_SUCCESS`), ten upraví obsah Redux úložiště a protože máme deklarativní React, tak se data automaticky překreslí.

![](753737db-6bc8-42cf-a46a-228ccba5283a/graphql-komunikace-crop.png) *** Obr. 1: příklad komunikace komponent s API

Server Fetcher
==============
V předchozí akci bylo vidět, že se volá funkce `fetch`. To je úplně jednoduchá funkce, která pošle na server GraphQL dotaz a vrátí odpověď (resp. Promise). Vlastně dělá jen to, že pomocí [isomorphic-fetch](https://github.com/matthew-andrews/isomorphic-fetch) položí dotaz ve správném formátu:

```javascript
return fetch(config.apiAddress, {
  method: 'POST',
  body: JSON.stringify({
    query: graphQuery, // přichází jako argument fetcheru
    variables: variables, // dtto
    operationName: operationName // dtto
  }),
  headers
}).then(response => response.json())
  .then(json => {
    return json; // zde budou příchozí data z API
  });
```

Poskládat potřebné hlavičky je otázkou několika málo sekund:

```javascript
let headers = {
  Accept: '*/*',
  'Content-Type': 'application/json'
};
let token = Authenticator.getToken(); // localStorage
if (token !== null) {
  headers.authorization = token;
}
```

Asi by bylo možné vracet rovnou pole `data`, které GraphQL API vrací, aby nebylo nutné dělat destructing v akcích, ale to už jsou jen kosmetické detaily.

Teď by tedy mělo být zřejmé následující:
- v Reactu existují kontejnery, což jsou komponenty, které **tahají data**
- existují také prezentační komponenty, které **jen vykreslují data** podle `props`
- kontejnery získávají data z Redux úložiště spuštěním akce při připojování kontejneru do DOMu (`componentWillMount`)
- součástí spuštění akce může být načtení těchto dat do úložiště (pokud tam již nejsou)
- na server se požadavky posílají jako jednoduchý POST s tělem obsahujícím GraphQL dotaz
- souběžně s tělem POST požadavku je nutné odeslat také autorizační hlavičky (pokud to aplikace vyžaduje)
OK? Možná to bylo rychlé, ale [mrkněte na kód](https://github.com/adeira/connector-frontend) a popř. si to vyzkoušejte. Nic složitého... :)

Přijetí požadavku na PHP serveru
================================
Dostáváme se do oblasti, ve které [se vypastí](https://forum.nette.org/cs/28370-data-z-post-request-body-reactjs-appka-se-po-ceste-do-php-ztrati) překvapivě hodně lidí. Na straně serveru je třeba přistupovat k POST datům [trošku jinak](https://github.com/adeira/connector/blob/c501227a4429dba493624ca9fa85745fb5f1839c/instances/Connector/Infrastructure/Delivery/Http/GraphqlEndpoint.php#L62), než by mohlo být zřejmé. K této trošce teorie bude potřeba následující PHP kód:

```php
<?php

var_dump(
  $_POST,
  file_get_contents('php://input')
);
```

Co se stane, pokud odešleme POST požadavek obsahující JSON třeba pomocí konzole v prohlížeči?

```javascript
var xhr = new XMLHttpRequest();
xhr.open("POST", "test.php", true);
xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded; charset=UTF-8");
xhr.send("{a:'b'}");
```

Vrátí se tato odpoveď:

```
array(1) {
  ["{a:'b'}"]=>
  string(0) ""
}
string(7) "{a:'b'}"
```

Obsah `$_POST` pole může být zvláštní, ale s tím by se dalo žít. Vzhledem k tomu, že se požadavek posílá jako formulář, tak se očekává trošku jiný formát dat:

```javascript
xhr.send("a=1&b=2");
```

V tomto případě pole pěkně expanduje:

```
array(2) {
  ["a"]=>
  string(1) "1"
  ["b"]=>
  string(1) "2"
}
string(7) "a=1&b=2"
```

Co se však stane, pokud změníme hlavičku požadavku `Content-Type`?

```javascript
var xhr = new XMLHttpRequest();
xhr.open("POST", "test.php", true);
xhr.setRequestHeader("Content-Type", "application/json; charset=UTF-8");
xhr.send("a=1&b=2");
```

POST pole bude zcela prázdné! (nikoliv však `php://input`)

```
array(0) {
}
string(7) "a=1&b=2"
```

Jak je totiž psáno v dokumentaci, tak `$_POST` neobsahuje všechna data, která jsou na server odeslána jako POST, nýbrž:

> An associative array of variables passed to the current script via the HTTP POST method **when using application/x-www-form-urlencoded or multipart/form-data as the HTTP Content-Type** in the request.

Druhá polovina věty je extrémně důležitá. Také je z předchozích ukázek vidět, že jediné místo, kde jsou data k dispozici je právě input stream `php://input`. Z toho důvodu je na serveru k JSON datům nutné přistupovat rovnou přímo pomocí `file_get_contents('php://input')` (tak to dělá vnitřně `Nette/Http/RequestFactory`) nebo pomocí `$httpRequest->getRawBody()`, což je úplně to samé, jen více schované a více objektové.

Nyní již stačí pouze ověřit uživatele, jestli se může API vůbec ptát, získat JSON, rozparsovat dotaz a poslat jej nějaké GraphQL knihovně ať se postará o všechny strasti tohoto API. Vše je vidět v tomto jednoduchém [GraphQL endpointu](https://github.com/adeira/connector/blob/c501227a4429dba493624ca9fa85745fb5f1839c/instances/Connector/Infrastructure/Delivery/Http/GraphqlEndpoint.php).

Kdyby někdo prahnul po pořádné náloži uceleného textu, tak je možné sledovat [tento soubor](https://github.com/mrtnzlml/dp-latex/blob/master/main.pdf) (stále rozdělaný), který jednou začas trošku povyroste. Nějaké základní znalosti problematicky jsou nutné, ale použitý jazyk by měl být srozumitelný většině lidí. Připomínky jsou vítány, než bude pozdě... :)
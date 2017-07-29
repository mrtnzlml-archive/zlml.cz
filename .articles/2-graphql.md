Z [minulého dílu](1-od-komponent-zpet-ke-komponentam) by mělo být všem jasné, jak jsem se dostal až sem. Od PHP komponent k webovým komponentám, které vlastně nejsou skutečné webové komponenty, ale jen kus JS kódu, který implementuje vlastní způsob webových komponent - React. Jsem přesvědčen o tom, že pro další pokračování je nutné vysvětlit, jak taková aplikace funguje. Takže...

# Jak taková aplikace funguje?

Jestli mě něco na JS světě už dlouhou dobu děsí, tak je to skutečnost, že vlastně nikdo neví, jak by taková aplikace měla vypadat. Ačkoliv je mnoho lidí přesvědčeno o své pravdě, neuvědomují si subjektivitu jejich tvrzení. Z toho důvodu je teď milion implementací a návrhů a každý to dělá trošku jinak. Pokud to však vezmu co nejvíce objektivně, tak by se taková aplikace dala popsat následovně:

- v prohlížeči běží JS kód, který se stará o vykreslování stránky s využitím veškeré síly JavaScriptu
- _volitelně_: JS kód posílá do prohlížeče třeba NodeJS server, který dokáže vyrenderovat JS a poslat rovnou hotovou stránku do prohlížeče (včetně připravených dat)
- na serveru běží kód (v mém případě Nette), který čeká na co se ho JS kód zeptá prostřednictvím (GraphQL) API a podle toho odpoví

Proč mi na serveru běží PHP, když hodně lidí preferuje mít JS i na serveru? Protože jsem skálopevně přesvědčen, že dokážu v PHP napsat lepší aplikaci s využitím veškerých myšlenek DDD co zvládnu pochopit (narozdíl od JS). End of story...

No a jak už titulek tohoto článku napovídá, tak mnou navrhovaný způsob je právě **GraphQL** ([link](http://graphql.org/)).

# GraphQL queries

GraphQL je nesmírně chytrý způsob jak se ptát API a přitom je to tak jednoduché, až mi přijde hloupé o tom psát. Pomocí GraphQL se lze serveru zeptat přímo na konkrétní věci. Tedy jako když se ptáte REST API, ale s tím rozdílem, že součástí požadavku je i informace o tom, co má API vrátit. Je dokonce možné zeptat se i více "endpointů" najednou. Zkuste si toto v REST API... :) Naopak GraphQL vyžaduje explicitní vyjmenování toho co chcete, takže jednoduše (pokud vím) nelze napsat dotaz, který by vrátil vše co daný endpoint umí.

Pojďme si to trošku vyzkoušet. Jako dobré hřiště pro dotazy poslouží [tato online aplikace](http://graphql-swapi.parseapp.com/). Dotazy se vždy posílají na jednu adresu (vetšinou `/graphql`) s tím, že se mění pouze obsah zprávy, který putuje v POST. To je velký rozdíl oproti REST API. Zde je jen jedna adresa, ale memí se obsah dotazu. Právě to přidává na dynamice dotazování - nejsme limitování na URL adresy. Takže když chceme vytáhnout z API např. všechny filmy, pošleme tento dotaz:

```
{
  allFilms {
    totalCount
    films {
      id
      title
      director
    }
  }
}
```

Tento zvláštní zápis říká, že se ptám na všechny filmy (`allFilms`) a zajímá mě kolik jich je. Zároveň chci u jednotlivých filmů vrátit jejich ID, název a režiséra. API mi pak vrátí dlouhý JSON:

```js
{
  "data": {
    "allFilms": {
      "totalCount": 6,
      "films": [
        {
          "id": "ZmlsbXM6MQ==",
          "title": "A New Hope",
          "director": "George Lucas"
        },
        ...
      ]
    }
  }
}
```

[Vyzkoušejte si to](http://graphql-swapi.parseapp.com/?query=%7B%0A%20%20allFilms%20%7B%0A%20%20%20%20totalCount%0A%20%20%20%20films%20%7B%0A%20%20%20%20%20%20id%0A%20%20%20%20%20%20title%0A%20%20%20%20%20%20director%0A%20%20%20%20%7D%0A%20%20%7D%0A%7D%0A&operationName=null). Chtěl bych ještě vědět jaké planety jsou ve filmu? Stačí rozšířit dotaz:

```
{
  allFilms {
    totalCount
    films {
      id
      title
      director
      planetConnection {
        planets {
          name
        }
      }
    }
  }
}
```

API vrátí ještě delší JSON. Vyzkoušejme jiný příklad. Co když mám k dispozici ID filmu, jak se zeptám pouze na ten konkrétní film? Pošleme ID filmu jako paramter dotazu:

```js
{
  film(id: "ZmlsbXM6MQ==") {
    title
  }
}
```

A teď ta nejvíce úžasná část. Chci si jedním šmahem vytáhnout film, člověka a všechny planety? Easy:

```js
{
  film(id: "ZmlsbXM6MQ==") {
    title
  }
  person(id: "cGVvcGxlOjE=") {
    name
  }
  allPlanets {
    planets {
      name
    }
  }
}
```

A výsledek? Ultra dlouhý JSON:

```js
{
  "data": {
    "film": {
      "title": "A New Hope"
    },
    "person": {
      "name": "Luke Skywalker"
    },
    "allPlanets": {
      "planets": [
        {
          "name": "Tatooine"
        },
        {
          "name": "Alderaan"
        },
        ...
      ]
    }
  }
}
```

Asi nemá smysl zanořovat se hlouběji. Princip by měl být jasný a samotné API je do jisté míry ovlivněno jeho návrhem (zde stránkováním). Jednoduše mohu jedním POST dotazem (což je ten zvláštní řetězec vypadající jako zjednodušený JSON) získat z API informace, které přesně moje React komponenta potřebuje. Toho některé knihovny silně využívají a vrácený výsledek posílají v properties přímo komponentě, který se stará **pouze** o vykreslování. Uvedu zde pouze krátký příklad toho co tím myslím (podrobněji to můžeme řešit později).

`DataSourcesContainer` je komponenta, která využívá [Apollo](http://dev.apollodata.com/react/) a cíl této komponenty je pouze vytáhnout data a vykresení delegovat někam dál (`Row`).

```js
const DataSourcesContainer = (props) => {
	let {data: {loading, devices}} = props;
	return loading ? null :
		<div>
			<h2>Data Sources</h2>
			{devices.map(dataSource =>
				<Row key={dataSource.id} dataSource={dataSource}/>
			)}
		</div>;
};

export default graphql(gql`
  query {
    devices {
      id,
      name,
      records
    }
  }
`)(DataSourcesContainer);
```

Až teprve `Row` se stará o vykreslení, ale už nikdy nikdy nepošle dotaz na API:

```js
const Row = (props) => {
	let ds = props.dataSource;
	return <div>{ds.name} <Link to={`/data-sources/${ds.id}`}>{ds.id}</Link> ({ds.records.length} records available)
	</div>;
};

Row.propTypes = {
	dataSource: React.PropTypes.shape({
		id: React.PropTypes.string,
		name: React.PropTypes.string,
		records: React.PropTypes.arrayOf(React.PropTypes.string)
	}).isRequired,
};

export default Row;
```

Nechápu proč si někdo říká React programátor... :))

# GraphQL mutations

Dobré API však potřebuje ještě minimálně jedu věc - možnost modifikace dat. K tomu slouží mutace. Zde opět musí aplikace (Nette) nadefinovat jaké jsou "endpointy" a jaké mají parametry. Taková mutace potom může vrátit nějaký datový typ a zde se to chová úplně stejně jako _queries_.

Tak kupříkladu přihlášení. Z mého pohledu je to mutace, protože měním stav aplikace a proto má moje aplikace připravenou tuto mutaci (toto je skutečný příklad z projektu [adeira/connector](https://github.com/adeira/connector) pokud se chcete šťourat v kódu):

```
type Mutation {
  login(username: String!, password: String!): User
}
```

Tzn. že pokud pošlu dotaz na to správné místo, tak mi API vratí uživatele, nebo mě odmítne. Využijeme CURL:

```
curl -XPOST -H "Content-Type:application/graphql" -d '{"query": "mutation {login(username:\"test\",password:\"test\"){id,token}}"}' http://connector.adeira.localhost/graphql | jq .
```

A protože jsem zadal správné přihlašovacé údaje, tak mi API vrátí ID a [JWT token](https://jwt.io/), protože o to jsem si v dotazu řekl:

```js
{
  "data": {
    "login": {
      "id": "4ff2f293-9d21-4407-a6af-08f766e06cb3",
      "token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzUxMiJ9.eyJpYXQiOjE0ODMxODE3OTksImV4cCI6MTQ4MzE4NTM5OSwidXVpZCI6IjRmZjJmMjkzLTlkMjEtNDQwNy1hNmFmLTA4Zjc2NmUwNmNiMyJ9.o2aHdbjgtg80e_yXdFJSy4gCTb-4exEbNQbaOK9xa7nyiLpfvYe0FBPizz0XUVrE1JDzkW9m3QnupiVtTDyZ2g"
    }
  }
}
```

Zde je nutné zdůraznit, že je naprosto zásadní, aby aplikace používala HTTPS. Co když zadám špatné heslo? API samozřejmě náležitě odpoví (včetně správného HTTP kódu):

```js
{
  "data": {
    "login": null
  },
  "errors": [
    {
      "message": "The password is incorrect.",
      "locations": [
        {
          "line": 1,
          "column": 11
        }
      ]
    }
  ]
}
```

Z API si tak můžu vytáhnout vše co potřebuju pro změnu stavu aplikace. V tomto případě je to jen [JWT token](https://jwt.io/), který si uložím třeba do local storage a jsem na frontendu přihlášen...

Tento článek se již natáhl více než bych si přál a proto jsem vypustil informaci o implementaci na straně PHP. To totiž vydá na samostatnou kapitolku, takže si to nechám na někdy jindy (možná hned příště, aby to šlo pěkně popořadě).

Podělte se prosím o postřehy.

Každý pozorný čtenář si také jistě všiml změny designu (nemluvě o přechodu na AWS) - líbí? :)
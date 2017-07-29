Kdysi mi někdo řekl, že správný programátor by měl být tak trošku děvka pro všechno. Nestačí jen umět PHP. Nestačí jen umět JavaScript. S tímto názorem jsem od samého začátku souhlasil. Ostatně je to jeden z důvodů, proč se občas zajímám i o věci, které v nejbližší době nevyužiju a zase tolik jim nerozumím (také to podle toho pak vypadá). Jednou z takových věcí je [React](http://facebook.github.io/react/index.html). Nedávno jsem si hrál také s [PhantomJS](http://phantomjs.org/) a když už jsme u toho, tak ani [NodeJS mi není cizí](http://www.slideshare.net/MartinZlmal/nodejs-42314371). A co se stane, když se jednoho večera rozhodnete spojit všechno dohromady? Něco šíleného... (-:

Krátké seznámení
================
Vzhledem k tomu, že tento blog byl vždy zacílen spíše na začátečníky, bylo by vhodné jednotlivé projekty krátce přiblížit.

[React](http://facebook.github.io/react/index.html) vytvořený Facebookem se sám prezentuje jako knihovna pro stavění uživatelských rozhraní. V sekci [Why React?](http://facebook.github.io/react/docs/why-react.html) se lze však dočíst zajímavější skorodefinici: *"Many people choose to think of React as the **V** in MVC."* Celá myšlenka Reactu je postavená na komponentách. A já mám [komponenty rád](https://doc.nette.org/cs/components). Myslím si, že je to dobrý směr jak udržet v aplikaci chaos na rozumné úrovni. Než abych to zde však dlouhosáhle popisoval, doporučuji pojet si [Getting Started](http://facebook.github.io/react/docs/getting-started.html).

[PhantomJS](http://phantomjs.org/) je oproti tomu úplně něco jiného. Jedná se o tzv. "headless browser" - velmi volně přeloženo prohlížeč bez grafického rozhraní. Je to takové Safari bez Safari. Prohlížeč, kterému úplně chybí (jinak pomalá) renderovací vrstva. Nejedná se tedy o prohlížeč, který by chtěl kdokoliv používat, ale spíše o nástroj, kterým lze skutečný prohlížeč nahradit třeba na serveru. Primárně se tedy používá pro testování aplikací, ve fantazii se však meze nekladou. Je tak možné např. tvořit screeny webů, nebo spouštět plně JavaScriptové stránky a koukat se na jejich obsah. Já jej začal používat kvůli rychlosti (oproti Seleniu), v tomto konkrétním případě však slouží právě pro spuštění čistě JavaScriptové stránky.

No a konečně [NodeJS](https://nodejs.org/en/) - JavaScriptové prostředí pro server. Zatímco ostatní jej využívají pro tvorbu aplikací, které jsou napsány v JS prakticky kompletně (server,  klient), já jej využíval kvůli svým vlastnostem spíše na rychlou komunikaci elektronika - server. To se mi na JS líbí. Dnes se nechají napsat i velmi složité věci pomocí pár řádek, ale třeba práce s časem a datumem bude vždy oser... (-:

Něco šíleného
=============
[* e80e305e-8431-4c0e-9c79-5db761c22608/199aff3.jpg 300x? >]

To je tak když se sejde několik věcí najednou. Vytvořit si faktury, otestovat aplikaci, neustálý přirozený hlad po NodeJS. A pak to přišlo. Co si tak vytvořit [generátor faktur](https://github.com/mrtnzlml/js-invoice-generator), který bude samotnou fakturu stavět pomocí Reactu, server bude tvořit NodeJS a PDF budu generovat tak, že si tu stránku otevřu v PhantomJS a uložím (čímž snad získám velmi přesný výsledek)? Samozřejmě jsem se pokukoval i po již hotových řešeních jako je třeba [Fakturiod](https://www.fakturoid.cz/). A kdybych byl jen o kousek línější, tak bych asi nic takového netvořil. Nakonec jsem se však pouze inspiroval [peckovým designem jedné z jejich faktur](https://www.fakturoid.cz/blog/2015/08/25/nova-verejna-stranka-faktury) (protože k designu mám asi tak daleko jako k Praze z Azor) s tím, že jí komplet napíšu znova pomocí komponent v Reactu a budu se modlit, aby to nikoho z Fakturoidu (až si tento článek přečte) neurazilo... (-:

Prvním krokem bylo [naškytnout samotný server](https://github.com/mrtnzlml/js-invoice-generator/blob/master/run.js). Zde bylo potřeba nastartovat ještě samotný PhantomJS pomocí [child_process](https://nodejs.org/api/child_process.html#child_process_child_process_spawn_command_args_options). Mohl jsem to udělaj jakkoliv jinak, ale takovou opičárnu jsem si vždy chtěl vyzkoušet. Samotná příprava stránky pro PhantomJS je pak [jednoduchá](https://github.com/mrtnzlml/js-invoice-generator/blob/master/invoice.js). Takže server funguje a PhantomJS běží. Co dál?

Chtělo by to něco co by šlo renderovat. Takové minimální rozumné HTML může vypadat fakt jednoduše:

```html
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8"/>
    <script src="react.js"></script>
</head>
<body>
    <div id="react-root"></div>
    <script src="build/index.js"></script>
</body>
</html>
```

Kde `react.js` je samotný React a `build/index.js` obsahuje definici komponent:

```jsx
var LikeButton = React.createClass({
	render: function () {
		return (
			<button>LikeButton</button>
		);
	}
});

React.render(<LikeButton />, document.getElementById('react-root'));
```

Je to fakt minimální, ale funkční kód, takže si to může vyzkoušet každý. Přechozí zápis samozřejmě není JavaScript, ale [JSX](https://facebook.github.io/jsx/), takže je třeba jej ještě přeložit (`jsx --watch src/ build/`). Výsledný kód je pak v tomto konkrétním jednoduchém případě velmi podobný:

```javascript
var LikeButton = React.createClass({displayName: "LikeButton",
	render: function () {
		return (
			React.createElement("button", null, "LikeButton")
		);
	}
});

React.render(React.createElement(LikeButton, null), document.getElementById('react-root'));
```

Pokud se vrátím zpět k faktuře, tak její HTML [je také velmi jednoduché](https://github.com/mrtnzlml/js-invoice-generator/blob/master/invoice.html) a celá sranda se odehrává [zde](https://github.com/mrtnzlml/js-invoice-generator/blob/master/src/react-invoice.js). Zde jsem si také schválně chtěl vyzkoušet, jak reálně funguje [Inline Styles](https://facebook.github.io/react/tips/inline-styles.html). Samotná faktura je tedy tvořena pouze krátkým HTML a pěknou kopou JavaScriptu. Žádné CSS. Abych řekl pravdu, tak se mi s tím moc dobře nepracuje, ale to je dáno spíše tím, že zase až tolik CSS nehovím a proto bylo místy komplikované přemýšlet nad tím jak funguje React a zároveň držet v hlavě to, jak má faktura nakonec vypadat. Koukal jsem se, jak se to řeší na jiných webech jedoucích na Reactu a asi se o zase až tak nepoužívá. Ale konec naříkání. Podívejte se [na výsledek](https://github.com/mrtnzlml/js-invoice-generator/blob/master/invoice.pdf).

QR kód
======
Ještě bych se rád zaměřil na samotný QR kód, který je na faktuře ([komponenta](https://github.com/mrtnzlml/js-invoice-generator/blob/master/src/react-invoice.js#L246-L269)). Použil jsem [stejnou knihovnu](https://larsjung.de/jquery-qrcode/), kterou používám zde na blogu v záhlaví (nejlepší). Protože se jedná o QR, tak se nedá čekat, že by obsahoval něco složitého ([podrobný popis formátu](http://qr-platba.cz/pro-vyvojare/specifikace-formatu/)):

```
SPD*1.0*ACC:CZ4101000000123456789101+KOMBCZPPXXX*AM:2750.00*CC:CZK*MSG:PLATBA FAKTURY 2015-0001*X-VS:20150001
```

Je to takový hloupý [RESP](http://redis.io/topics/protocol). Řetězec začíná označením formátu `SPD*1.0*` a následuje vždy výčet položek `klíč:hodnota*`, které chceme do QR kódu dostat. Povinný je pouze `ACC`, což je IBAN, popř. IBAN+BIC. Následuje částka, měna, zpráva pro příjemce a variabilní symbol.

Takže úkol někdy na příště. Přepsat **V** u blogu do Reactu? (-:
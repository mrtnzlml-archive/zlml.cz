On: *A budeš o tom psát nějaké články?*<br>
Já: *No tak jako mohl bych... :)*

...a tak tedy začínám krátký seriál a uvidím kam se za těch pár dílů dostanu.

Jednou z bezesporu nejzajímavějších vlastností Nette Frameworku jsou komponenty (myslím potomky `Nette\Application\UI\Control`). Komponenta je logický prvek na stránce, který umožňuje uschovat způsob vykreslování nějakého elementu stránky. Prakticky jsem začal komponenty využívat téměř ke všemu a čím více komponent, tím jsem spokojenější. Na stránce je potom místo mnoha složitých šablon mnohdy velmi rozvětvený strom komponent, kdy každá komponenta řeší jak vykreslit třeba část hlavičky (klidně i ve více variantách).

Systém komponent má však v Nette celou řadu úskalí a temných zákoutí. Jedním z největších problémů považuji to, že si komponenta nenese vše s sebou. Zejména pak styly a javascript. Ačkoliv jsem tento problém řešil pokaždé a vždy nějakým jiným originálním způsobem, vždy jsem došel ke stavu aplikace, který se mi z nějakého důvodu moc nelíbil.

Začal jsem tedy hledat jiné řešení. A než ukážu výsledek, je nutné pochopit kudy jsem šel a kde jsem se cestou popálil...

# WebComponents

[Webové komponenty](http://webcomponents.org/) jsou přesně to co jsem hledal. Webová komponenta slouží k rozšíření DOMu o vlastní HTML prvky (stejně tak jako funguje např. `<video>` tag). Pokud chcete vidět obsah takového tagu, doporučuji v Chrome zapnout "Show user agent shadow DOM". Přirozeně jsem šel hned za Polymerem, protože jej již nějaký čas znám. Nikdy jsem si ho však nevyzkoušel na reálném projektu. Minimální definice Polymer elementu vypadá takto:

```html
<dom-module id="element-name">
  <template>
    <style>/* ... */</style>
  </template>
  <script>
    Polymer({
      is: 'element-name',
    });
  </script>
</dom-module>
```

K dispozici tedy budu mít v HTML element `<element-name/>` (pozor: název musí obsahovat pomlčku). Následně bude mít komponenta definovaný způsob vykreslování, vlastní styly i vlastní JS chování. Super. Pokud chcete něco takového implementovat pomocí Nette komponent - vzdejte to rovnou. Neříkám, že by to nešlo, ale jednak je to složité a jednak bude supersložité řešit věci jako je [vulkanizace a použití Crisperu](https://www.polymer-project.org/1.0/docs/tools/optimize-for-production). Rychle jsem tedy opustil cestu vytváření komponent na straně serveru a vše jsem předal javascriptu.

Bylo to super a pak jsem **po několika měsících práce s Polymerem celou aplikaci rozerval na kousky a přepsal ji do něčeho jiného**. Nikdy jsem nezažil nic tak vyčerpávajícího jako práci s Polymerem. HTML se totiž bohužel chová tak, že pokud nějaký element nezná, tak jej ignoruje. A prakticky jakákoliv chyba typu "proč to nejde" byla způsobena práce tímto. Každý element je třeba importovat a ačkoliv IDE dokáže hodně pomoci, tak existují případy, kdy to prostě nefunguje a jen kvůli tomu, že jsem si prvek nenaimportoval...

```html
<input is="my-input">
```

Toto je přesně případ se kterým si třeba WebStorm nedokáže poradit. Takže fakticky je to moje chyba, že jsem nepozorný, ale o to více potřebuji, aby když něco udělám blbě, tak mi to pořádně nafackovalo a já hned věděl. Proto jsem Polymer opustil (ano to je jediný důvod) a ačkoliv si myslím, že je to skvělý nápad a webovým komponentám hodně fandím, vydal jsem se o kousek dál.

# React

Stejně tak jako všichni, tak i já jsem skončil u Reactu. A stydím se za to. Nechci jej používat jen proto, že je to cool. Ale dal jsem mu poprvé velkou šanci i tak (už jej znám dlouho, ale nikdy jsem nepotřeboval). Z mého pohledu: zatímco Polymer umí by default úplně [neuvěřitelně vymakané frontend prostředí](https://beta.webcomponents.org/collection/Polymer/elements), React neumí nic než pracovat s daty. Ale jeden ze zásadních rozdílů je v tom, že je striktní. Takže moje problémy s neexistujícími emlementy řeší JSX by design (kdo zná XHP, tak ví). Vzdal jsem se tedy úžasného nástroje a přešel jsem na něco, co umí v základu jen chytře aktualizovat DOM a vykreslit `div` (takto vypadá nejmenší komponenta, kterou lze v Reactu napsat - ES6):

```js
import React from 'react';

export default () => <div>Alenka</div>
```

V Reactu jsou dva druhy komponent - tzv. kontejnery a samotné prezentační komponenty. Toto rozdělení mi také vyhovuje, protože se hezky separují závislosti (kontejner pouze tahá data a prezentační komponenta pouze vykresluje vytažená data). Navíc si taková komponenta nese i vlastní styly (což se ne všem líbí, ale já tleskám):

```js
import './AuthorizedBase.css';
```

Podobně je možné importovat i obrázky a další zdroje díky Webpacku (neuvěřitelně WTF WOW nástroj). Webpack je právě nástroj, který řeší většinu bolístek, které jsem řešil složitě u Nette komponent.

Ačkoliv zrovna v mém případě React docela dává smysl, protože se bude hodně pracovat s daty, použil bych je i na místě, kde to totálně nedává smysl (tedy třeba na blog). Je to totiž způsob jak pracovat s frontendem takovým způsobem, jaký jsem dlouho hledal (navíc je React jednoduchý). Má to však jedno velké ALE.

Pokud bych vzal React a začal jej používat rovnou tak jak je, tak bych vytvořil pěknou hovadinu. Je potřeba milion dalších podpůrných služeb a vychytávek jako je třeba server-rendering a code-splitting. Bez těchto věcí, které je potřeba vyřešit, to nemá ani smysl zkoušet. Na to upozorňuji rovnou. Souhlasím s tím, že dělat to bez toho by byla prostě [ultrapíčovina](https://medium.com/@vojta/airbanko-v%C3%ADte-pro%C4%8D-m%C3%A1te-posran%C3%BD-web-629d96946576#.tshx84cqg). Z toho by mělo být patrné, že nechci profitovat ani tak třeba z chytrých aktualizací DOMu, ale ze způsobu jak se pracuje v Reactu s komponentami. Ve výsledku bude naprosto v pořádku, když nepůjde poznat, že je stránka napsána v Reactu (čehož asi stejně nedosáhnu). Důležité je dodržovat rozdělení komponent podle jejich účelu (tahání dat vs. vykreslování).

Toto byl jen krátký úvod. V dalších dílech se podívám na to, jak tahat data ze serveru pomocí GraphQL a jakou použít knihovnu. Zároveň je potřeba vyřešit napojení na Nette backend (protože PHP mě pořád moc baví... :)). Podíváme se také na routování, autorizaci modelové vrstvy, strukturu aplikace, renderování na serveru, testování, Webpack, code-splitting a další a další... Stay tuned. :)
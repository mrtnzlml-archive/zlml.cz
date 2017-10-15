---
timestamp: 1488119652000
title: Jak na lokální CSS pro React
slug: jak-na-lokalni-css-pro-react
---
Když jsem opouštěl koncept [webových komponent](1-od-komponent-zpet-ke-komponentam) a přecházel jsem k Reactu, tak mě nejvíce mrzelo, že přijdu o všechny zajímavé vlastnosti shadow DOMu. Ve webových komponentách se to má tak, že jak JS tak CSS jsou součástí jedné šablony a neovlivňují globální prostor. To je u Reactu bez problému z pohledu JS. Z pohledu CSS je to však jiná pohádka. Jakýkoliv styl definovaný v rámci komponenty se definuje pro celou aplikaci. Příklad jednoduché hlavičky:

```js
import React from 'react';
import './Header.css';

export default () =>
  <div className="header">
	  {/* další JSX hlavičky */}
  </div>
```

Kdy CSS soubor obsahuje styly pro header:

```css
.header {
  /* nějaké ty styly */
}
```

Díky Webpacku je možné takto načítat CSS styly v komponentě, ale `.header` je k dispozici v celé aplikaci (pokud je tam komponenta použita). Pak ale komponenty trošku postrádají smysl a mohl bych to klidně zase patlat v jednom hlavním CSS souboru. Vlastně jsem vůbec nic nezískal a musím si dávat velký pozor na to, co dělám.

Naštěstí existuje jednoduché řešení a tím jsou [CSS moduly](https://github.com/css-modules/css-modules). Stačí pouze trošku změnit Webpack nastavení a chování CSS se kompletně změní. Zatímco původní nastavení bylo takové:

```js
module.exports = {
  module: {
    rules: [
      {
        test: /\.css$/,
        loader: 'style-loader!css-loader?importLoaders=1!postcss-loader'
      }
    ]
  }
}
```

Tak nové bude vypadat takto:

```js
module.exports = {
  module: {
    rules: [
      {
        test: /\.css$/,
        use: [
          {
            loader: 'style-loader'
          },
          {
            loader: 'css-loader',
            options: {
              importLoaders: 1,
              modules: true, // Enable/Disable CSS Modules
              localIdentName: '[name]__[local]--[hash:base64:5]',
            }
          },
          {
            loader: 'postcss-loader'
          }
        ]
      }
    ]
  }
}
```

Je to vlastně ta samá konfigurace, jen jsem zapnul CSS moduly. Od této chvíle se veškeré CSS chovají lokálně pouze pro danou komponentu. Je třeba jen upravit použití těchto nových stylů v komponentě:

```js
import React from 'react';
import styles from './Header.css';

export default () =>
  <div className={styles.header}>
	  {/* další JSX hlavičky */}
  </div>
```

Co se stane s výstupem? Místo CSS třídy se budou generovat (+-) unikátní idenfikátory (s nějakou nápovědou pro development):

```html
<div class="Header__header--3dxwh">
  <!-- další HTML hlavičky -->
</div>
```

Resp. na produkci kde není nastaveno `localIdentName` takto:

```html
<div class="_3dxwhqIVz2ZHHrfQ6crpKp">
  <!-- další HTML hlavičky -->
</div>
```

Tím se zajistí, že CSS bude fungovat jen v rámci jedné komponenty. Jenže to není úplně ultimátní řešení. Některé styly je dobré mít dostupné v celé aplikaci. No tak třeba styly základních HTML elementů jsou stále definovány jako globální. Pokud však potřebujeme nějakou CSS třídu, tak je to možné pomocí `:global` prefixu:

```css
:global .clearfix:after {
  content: "";
  display: table;
  clear: both;
}

:global .wrapper--fluid {
  margin-left: 5rem;
  margin-right: 5rem;
}
```

Ačkoliv je z tohoto útržku vidět pozůstatek BEM zápisu, tak už to prakticky není potřeba.

Podívejte se, jak vypadá [taková změna](https://github.com/adeira/connector-frontend/commit/e3106bd84952da4350af0d9a9329a3f747724571) v jednom commitu. To řekne více než 1000 slov... :)
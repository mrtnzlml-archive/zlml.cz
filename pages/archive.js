// @flow

import * as React from 'react';

import Layout from '../components/Layout';
import Logo from '../components/Logo';
import Link from '../components/markup/Link';
import Strong from '../components/markup/Strong';
import Paragraph from '../components/markup/Paragraph';

type Props = {
  articles: Array<{|
    title: string,
    slug: string,
    date: string,
  |}>,
};

const Archive = ({ articles }: Props) => (
  <Layout title="Archive">
    <Logo />

    <Paragraph>
      <Strong>🚶 This blog has been discontinued!</Strong>
    </Paragraph>

    {articles.map((article, index) => {
      return (
        <div key={article.slug}>
          <span style={{ color: '#aaa', paddingRight: '1rem' }}>
            {article.date}
          </span>
          <Link
            href={`/p/${article.slug}`}
            as={article.slug}
            prefetch={index === 0} // prefetch first article
          >
            {article.title}
          </Link>
        </div>
      );
    })}
  </Layout>
);

Archive.getInitialProps = async function(): Promise<Props> {
  const articles = [];
  for (const article of ArticlesDatabase) {
    articles.push({
      title: article.title,
      slug: article.slug,
      date: new Date(article.timestamp).toISOString().slice(0, 10),
    });
  }
  return {
    articles: articles,
  };
};

export default Archive;

const ArticlesDatabase = [
  {
    timestamp: 1510619991384,
    title: 'Persist your GraphQL queries!',
    slug: 'persist-your-graphql-queries',
    preview:
      'GraphQL is very powerful and I really love it. But it&#39;s not really production ready by default. There is a lot of things you need to prepare and o...',
  },
  {
    timestamp: 1509494028280,
    title: 'GraphQL Summit 2017 - San Francisco',
    slug: 'graphql-summit-2017-san-francisco',
    preview:
      'GraphQL Summit was really a great eye-opener. I never thought about GraphQL in this way and I never realized how is the company I am working for falli...',
  },
  {
    timestamp: 1507943214291,
    title: 'I deleted PHP',
    slug: 'i-deleted-php',
    preview:
      'The most expensive server is the one doing nothing. And that was basically happening with this blog. I always had some kind of shared hosting or VPS b...',
  },
  {
    timestamp: 1500731161000,
    title: 'How do we do serverless?',
    slug: 'how-do-we-do-serverless',
    preview:
      'In past few months, I moved completely from PHP backend to the JavaScript semi-frontend (not really backend but definitely not frontend - somewhere be...',
  },
  {
    timestamp: 1497708814000,
    title: 'Get rid of GraphQLNonNull',
    slug: 'get-rid-of-graphqlnonnull',
    preview:
      'I mean not all of the occurrences but at least in GraphQL fields. This is exactly what we did in Kiwi.com while working on GraphQL API.\nThis idea didn...',
  },
  {
    timestamp: 1490538013000,
    title: 'React + Redux - Apollo =  FapFap',
    slug: 'react-redux-apollo-fapfap',
    preview:
      'Na konci minulého roku jsem začal něco jako virtuální seriál o React vs. PHP aplikaci. Včera jsem na Poslední sobotě byl upozorněn na to, že už asi ne...',
  },
  {
    timestamp: 1488119652000,
    title: 'Jak na lokální CSS pro React',
    slug: 'jak-na-lokalni-css-pro-react',
    preview:
      'Když jsem opouštěl koncept webových komponent a přecházel jsem k Reactu, tak mě nejvíce mrzelo, že přijdu o všechny zajímavé vlastnosti shadow DOMu. V...',
  },
  {
    timestamp: 1488109164000,
    title: '4 způsoby práce s formulářem v šabloně',
    slug: '4-zpusoby-prace-s-formularem',
    preview:
      'Velká část článků na tomto blogu jsou reakcí na nějaký reálný problém. Nehledě na to, kde jsem (třeba na včerejším React workshopu), tak odpovídám na ...',
  },
  {
    timestamp: 1487322556000,
    title: 'Hexagonální architektura',
    slug: 'hexagonalni-architektura',
    preview:
      'Struktura webových aplikací je něco, co se neustále mění a stejně s programátorem i zdokonaluje. Před více než rokem jsem se o jedné z možných struktu...',
  },
  {
    timestamp: 1485965763000,
    title: 'Řešení N+1 problému v GraphQL',
    slug: 'reseni-n-1-problemu-v-graphql',
    preview:
      'Na 85. Poslední sobotě v Praze jsem měl workshop a přednášku o GraphQL. Na konci přednášky padl velmi dobrý dotaz ohledně toho, jestli náhodou netrpí ...',
  },
  {
    timestamp: 1484849186000,
    title: 'Vy ještě nemáte svůj superprojekt?!',
    slug: 'vy-jeste-nemate-svuj-superprojekt',
    preview:
      'Nenechte se ošálit. Superprojekt je skutečně oficiální název pro Git projekty, které virtuálně obsahují další podprojekty (tzv. submoduly). Jedná se o...',
  },
  {
    timestamp: 1483183709000,
    title: 'GraphQL',
    slug: '2-graphql',
    preview:
      'Z minulého dílu by mělo být všem jasné, jak jsem se dostal až sem. Od PHP komponent k webovým komponentám, které vlastně nejsou skutečné webové kompon...',
  },
  {
    timestamp: 1482501056000,
    title: 'Muskulaturní rozšíření DIC',
    slug: 'muskulaturni-rozsireni-dic',
    preview:
      'K čemu je DI rozšíření v Nette a jak se takové rozšíření píše už víme. Teď se podíváme na způsob, jak pracovat s takovým rozšířením na úplně nové úrov...',
  },
  {
    timestamp: 1481912287000,
    title: 'Od komponent zpět ke komponentám',
    slug: '1-od-komponent-zpet-ke-komponentam',
    preview:
      'On: A budeš o tom psát nějaké články?\nJá: No tak jako mohl bych... :)\n...a tak tedy začínám krátký seriál a uvidím kam se za těch pár dílů dostanu.\nJe...',
  },
  {
    timestamp: 1480773916000,
    title: 'Testování korelací v MATLABu',
    slug: 'testovani-korelaci-v-matlabu',
    preview:
      'Čím více mi proteklo kódu pod prsty, tím více mě testování baví. Nebaví mě psát super skvělé testy, ale baví mě brát testy jako součást vývoje. Mnohdy...',
  },
  {
    timestamp: 1478454487000,
    title: 'Tester: vlastní Output Handler',
    slug: 'tester-vlastni-output-handler',
    preview:
      'Output Handler umožňuje změnit finální podobu výstupu z Nette Testeru. Výstup může vypadat například takto. Osobně se mi tento výstup líbí víc, protož...',
  },
  {
    timestamp: 1475925989000,
    title: 'Automatický render prvků při manuálním vykreslování formuláře',
    slug: 'automaticky-render-prvku-pri-manualnim-vykreslovani-formulare',
    preview:
      'Je čas na nějakou tu Nette divočinu. Podívejme se pod drobnohledem na to, jak funguje vykreslování prvků formuláře. Nejprve si vytvoříme úplně jednodu...',
  },
  {
    timestamp: 1474881018000,
    title: 'Shhhh - vysílá se speaker',
    slug: 'shhhh-vysila-se-speaker',
    preview:
      'Takové bylo téma celého Webexpa 2016 - shhhh. Nebo alespoň páteční části. Pro ty nejlepší a nejzajímavější řečníky byl totiž připraven velký sál, kam ...',
  },
  {
    timestamp: 1471811573000,
    title: '10 obsesí na WebExpu 2016',
    slug: '10-obsesi-na-webexpu-2016',
    preview:
      'Obsese ž. kniž. chorobně utkvělá představa, myšlenka; med. nutkavé jevy, nejčastěji myšlenky, popudy k jednání apod., jež jsou nesmyslné a bezdůvodně ...',
  },
  {
    timestamp: 1470502440000,
    title: 'More awesome Monolog for',
    slug: 'more-awesome-monolog-for-nettefw',
    preview:
      'Nedávno mi přišel požadavek na vytvoření takového jednoduchého způsobu, jak logovat uživatelské akce - konkrétně zatím jen přihlášení uživatele (do da...',
  },
  {
    timestamp: 1469950790000,
    title: 'Dva šablonovací systémy zároveň',
    slug: 'dva-sablonovaci-systemy-zaroven',
    preview:
      'Možná pracujete na nějakém projektu, který používá jiný šablonovací systém než je Latte, ale Latte se vám natolik líbí, že ho chcete používat také. Ne...',
  },
  {
    timestamp: 1469363764000,
    title: 'Testbench 2.3 is out (finally)',
    slug: 'testbench-2-3-is-out-finally',
    preview:
      'Yeah, you heard that correctly. Testbench 2.3 has been released and it brings a lot of new features. I don&#39;t want to write about small bugfixes bu...',
  },
  {
    timestamp: 1466343012000,
    title: 'Fix compatibility with Nette 2.4',
    slug: 'fix-compatibility-with-nette-2-4',
    preview:
      'Právě v těchto dnech přichází další významná minor verze Nette balíčků do vašich vendorů. Událo se velké množství změn a to zejména pod kapotou. To si...',
  },
  {
    timestamp: 1460891805000,
    title: 'PSR-4 autoloader aplikace',
    slug: 'psr-4-autoloader-aplikace',
    preview:
      'Nikdy jsem moc nelpěl na PSR-FIG pravidlech. Částečně možná proto, že jsem je nikdy moc nechápal, částečně možná proto, že nemám rád, když mi někdo ně...',
  },
  {
    timestamp: 1459632882000,
    title: 'Magie zvaná mapping presenterů',
    slug: 'magie-zvana-mapping-presenteru',
    preview:
      'Ona to vlastně ani není taková magie jako to není nikde pořádně popsané. Než se pustím to obludných složitostí, bylo by vhodné zmínit se co to vlastně...',
  },
  {
    timestamp: 1457796154000,
    title: 'Rozšíření pro DIC',
    slug: 'rozsireni-pro-dic',
    preview:
      'Jednu z věcí, které jsem zde na blogu moc nepopsal jsou rozšíření pro DIC (Dependency Injection Container, potomek Nette\\DI\\Container). A protože se c...',
  },
  {
    timestamp: 1454786953000,
    title: 'Jednoduché testování pro úplně každého',
    slug: 'jednoduche-testovani-pro-uplne-kazdeho',
    preview:
      'Konec slibů, článek je tu. Tentokrát se rozepíšu o nástroji Testbench. Testbench by měl pomoci s rychlým testováním Nette aplikace. Je zaměřen spíše i...',
  },
  {
    timestamp: 1449682578000,
    title: 'ZČU - Nette je fajn, ale máme raději Javu',
    slug: 'zcu-nette-je-fajn-ale-mame-radeji-javu',
    preview:
      '\nDnes jsem měl opět tu čest přednášet studentům předmětu KIV/WEB na fakultě aplikovaných věd (ZČU) o tom, jak se dají dělat webové aplikace pomocí web...',
  },
  {
    timestamp: 1448405176000,
    title: 'Znovupoužitelné části formuláře',
    slug: 'znovupouzitelne-casti-formulare',
    preview:
      'Před nějakým časem jsem psal o tom, jak vytvořit znovupoužitený formulář. Nejedná se o nic jiného, než o správné navržení a následné použití komponent...',
  },
  {
    timestamp: 1447611844000,
    title: 'Od indexu až po presenter',
    slug: 'od-indexu-az-po-presenter',
    preview:
      'Když jsem se učil pracovat s Nette Frameworkem, musel jsem v začátcích hodně přivírat oči a říkat si &quot;prostě to tak je&quot;. Hodně věcí bylo zah...',
  },
  {
    timestamp: 1444573684000,
    title: 'Ještě lepší struktura Nette aplikace',
    slug: 'jeste-lepsi-struktura-nette-aplikace',
    preview:
      'Každý, kdo postavil pár aplikací, musel vždy řešit ten samý problém. Jakou strukturu by měla aplikace mít? A co když se začne projekt rozrůstat? Měl b...',
  },
  {
    timestamp: 1443869668000,
    title: 'Crazy JavaScript PDF generator',
    slug: 'crazy-javascript-pdf-generator',
    preview:
      'Kdysi mi někdo řekl, že správný programátor by měl být tak trošku děvka pro všechno. Nestačí jen umět PHP. Nestačí jen umět JavaScript. S tímto názore...',
  },
  {
    timestamp: 1440940560000,
    title: 'Návrhový vzor Legacy code',
    slug: 'navrhovy-vzor-legacy-code',
    preview:
      'Asi každý se k tomuto návrovému vzoru jednou dostane. Zatím jsem vždy takovou práci striktně odmítal, ale tentokrát šlo o jinou situaci a svolil jsem ...',
  },
  {
    timestamp: 1436618301000,
    title: 'Hierarchický router',
    slug: 'hierarchicky-router',
    preview:
      'Tento článek volně navazuje na předchozí. Zde jsem ukázal, jak vytvořit routy tak, aby bylo možné mít zcela libovolnou adresu a routovat ji na jakouko...',
  },
  {
    timestamp: 1436098908000,
    title: 'Dynamické routování URL adres',
    slug: 'dynamicke-routovani-url-adres',
    preview:
      'A když říkám dynamické, tak tím myslím opravdu kompletně. Jinými slovy to znamená, že chceme jakoukoliv cestu za doménou přeložit na jakýkoliv interní...',
  },
  {
    timestamp: 1435428633000,
    title: 'Vzhůru dolů! A pak zase nahoru...',
    slug: 'vzhuru-dolu-a-pak-zase-nahoru',
    preview:
      'Na relativně dlouhou dobu jsem se teď odmlčel. Psal jsem totiž někam jinam než na blog a svůj příděl písmenek na měsíc jsem odevzdával právě tam. Ale ...',
  },
  {
    timestamp: 1432200241000,
    title: 'Fixněte si databázi',
    slug: 'fixnete-si-databazi',
    preview:
      'Možná to znáte. Již nějaký čas nepoužíváte žádný SQL soubor a strukturu databáze si generujete z entit pomocí Doctrine. Je to super, rychlé a funguje ...',
  },
  {
    timestamp: 1430044733000,
    title: 'Doctrine pro non-doctrine programátory',
    slug: 'doctrine-pro-non-doctrine-programatory',
    preview:
      'A také o tom proč jsem se ptal kdo studoval, studuje, nebo bude studovat elektrotechnickou fakultu a proč jsem si až pak uvědomil, že jsem se vlastně ...',
  },
  {
    timestamp: 1427631321000,
    title: 'Barák budoucnosti',
    slug: 'barak-budoucnosti',
    preview:
      'Aneb jak jsem poprvé a naposledy přešlápl. Je to jednoduché, nebudeme si nic nalhávat. Ještě jsem nepochopil smysl Barcampu. A tak se stalo, že jsem s...',
  },
  {
    timestamp: 1427231722000,
    title: 'Generované továrničky - definitive guide',
    slug: 'generovane-tovarnicky-definitive-guide',
    preview:
      'No dobře, možná ne úplně definitivní, ale užitečná příručka snad. Pokusím se zde rozebrat všechny potřebné stavy generovaných továrniček, které považu...',
  },
  {
    timestamp: 1420324989000,
    title: 'Dva příklady toho, proč není JS připravený',
    slug: 'dva-priklady-toho-proc-neni-js-pripraveny',
    preview:
      'Tento článek nastartoval jeden hloupý tweet. Měl jsem jej na &quot;to do&quot; listu již nějaký čas, ale čekal jsem až to někdo tweetne... (-:\n@Martin...',
  },
  {
    timestamp: 1419375234000,
    title: 'RESP protokol - přímý přístup k Redis databázi',
    slug: 'resp-protokol-primy-pristup-k-redis-databazi',
    preview:
      'RESP (REdis Serialization Protocol) je něco, s čím se asi většina lidí nepotká. Důvod je prostý. Tento protokol je většinou zabalen hluboko v knihovně...',
  },
  {
    timestamp: 1418241190000,
    title: 'Poslední přednáška na FAV - Nette',
    slug: 'posledni-prednaska-na-fav-nette',
    preview:
      'Tak a je to... (-:\nZveřejňuji mojí poslední přednášku v tomto roce, kterou jsem měl na ZČU na fakultě aplikovaných věd pro studenty předmětu KIV/WEB. ...',
  },
  {
    timestamp: 1417636504000,
    title: 'Přednáška na ZČU - Node.js',
    slug: 'prednaska-na-zcu-node-js',
    preview:
      'Jak jsem slíbil, tak zveřejňuji přenášku na téma Node.js, kterou jsem měl dnes na Západočeské univerzitě pro lidi studující předmět KIV/WEB, tedy Webo...',
  },
  {
    timestamp: 1416264254000,
    title: 'Nadvláda inteligentních procesorů',
    slug: 'nadvlada-inteligentnich-procesoru',
    preview:
      'Pár dní zpátky jsem tweetoval o tom, nad čím právě teď trávím asi nejvíce času. Cílem celého mého snažení je dostat data z procesoru, který obsluhuje ...',
  },
  {
    timestamp: 1414868997000,
    title: 'Dependent select box',
    slug: 'dependent-select-box',
    preview:
      'Občas je v Nette zapotřebí vyřešit dependent select box. Je to relativně málo častý požadavek a o to méně se o něm dá najít, když je to zrovna potřeba...',
  },
  {
    timestamp: 1414243893000,
    title: 'Heatmapy ve Wolfram Mathematica',
    slug: 'heatmapy-ve-wolfram-mathematica',
    preview:
      '\nPrávě teď jsem řešil jak vizualizovat nějaká data, která jsou v maticovém formátu. Pro mé účely jsou prakticky dva grafy. Prvním grafem je heatmapa (...',
  },
  {
    timestamp: 1413669750000,
    title: 'Nastavení TIM3 na STM32F207xx',
    slug: 'nastaveni-tim3-na-stm32f207xx',
    preview:
      'Tento článek už mám dlouhou dobu v hlavě, ale nikdy jsem se neodvážil jej sepsat. Má to svůj smysl. Jedná se o poměrně náročnou problematiku, kterou s...',
  },
  {
    timestamp: 1413406213000,
    title: 'Fígloidní odstranění záhlaví modulu',
    slug: 'figloidni-odstraneni-zahlavi-modulu',
    preview:
      'Dnešní článek bude spíše zápisek, protože jsem řešení tohoto problému hledal neskutečně dlouho a jak se později ukázalo, tak řešení je sice jednoduché...',
  },
  {
    timestamp: 1411922332000,
    title: 'Hledá se obchodník',
    slug: 'hleda-se-obchodnik',
    preview:
      'Tentokrát nebudu psát o ničem technickém, ale vezmu to hned od druhé věty vážně. Do Orgis IT scháníme obchodníka primárně pro Prahu / Plzeň pro rozšíř...',
  },
  {
    timestamp: 1409409052000,
    title: 'Kdyby\\Console',
    slug: 'kdyby-console',
    preview:
      'Existují knihovny, bez kterých bych si vývoj webových aplikací již téměř nedokázal představit. Jedním z nich je Kdyby\\Console. Již dříve jsem sice nap...',
  },
  {
    timestamp: 1409163060000,
    title: 'Komunikace s ERP pomocí XML-RPC',
    slug: 'komunikace-s-erp-pomoci-xml-rpc',
    preview:
      'Spousta lidí by se ráda připojovala na API ERP systému Odoo, ne vždy je to však procházka růžovým sadem, protože se očekává místy až přehnaná interní ...',
  },
  {
    timestamp: 1406926545000,
    title: 'Za hranicí ORM',
    slug: 'za-hranici-orm',
    preview:
      'Již mnohokrát jsem slyšel, že je ORM antipattern. Já si to nemyslím. Je to hloupý a uspěchaný názor. V dnešním článku však nechci rozebírat co je a co...',
  },
  {
    timestamp: 1405627130000,
    title: 'SračkoAPI',
    slug: 'srackoapi',
    preview:
      'Následující řádky budou čistý hate na několik tvůrců API, který má posloužit budoucím tvůrcům API. Sám totiž musím obsluhovat několik služeb a získáva...',
  },
  {
    timestamp: 1405252851000,
    title: 'Novinky na blogu a extension RFC',
    slug: 'novinky-na-blogu-a-extension-rfc',
    preview:
      'Kdo pravidelně sleduje můj twitter, tak už to ví. A je to skvělé! Včera jsem totiž mergnul důležitou část tohoto blogu a tím vydal verzi 1.1 snad stab...',
  },
  {
    timestamp: 1402842853000,
    title: 'Čteme Data Matrix bez čtečky',
    slug: 'cteme-data-matrix-bez-ctecky',
    preview:
      '\nDnešním článkem navazuji na dřívější článek Čteme QR kódy bez čtečky, ve kterém jsem řešil čtení QR kódu bez použití jakéhokoliv čtecího zařízení. A ...',
  },
  {
    timestamp: 1402601786000,
    title: 'Disqus lazy loading',
    slug: 'disqus-lazy-loading',
    preview:
      'Tento článek ve skutečnosti odstartovalo zdánlivě nesouvisející vlákno na Nette fóru .{target:_blank}. V tomto vláknu se řeší parametr _fid v URL adre...',
  },
  {
    timestamp: 1400490051000,
    title: 'Stáhněte si lepší blog',
    slug: 'stahnete-si-lepsi-blog',
    preview:
      'Čas od času se na Nette fóru najde někdo, kdo hledá vzorový projekt do kterého by se mohl podívat. Vlastně se většinou hledá cokoliv, jakákoliv inspir...',
  },
  {
    timestamp: 1399974862000,
    title: 'Znovupoužitelný formulář',
    slug: 'znovupouzitelny-formular',
    preview:
      'Každý kdo nějakou chvíli pracuje s Nette Frameworkem již jistě narazil na prvky, které lze použít v aplikaci opakovaně. Říkejme jim znovupoužitelné ko...',
  },
  {
    timestamp: 1397388682000,
    title: 'Plzeňský Barcamp - láska na první pohled',
    slug: 'plzensky-barcamp-laska-na-prvni-pohled',
    preview:
      'Ačkoliv jsem z dřívějších barcampů sledoval záznamy, tak jsem byl fakticky na barcampu úplně poprvé a rovnou jsem měl tu čest přednášet. Než se však d...',
  },
  {
    timestamp: 1394985629000,
    title: 'Orion login stojí za prd',
    slug: 'orion-login-stoji-za-prd',
    preview:
      'Když jsem dříve připravoval prezentaci o Nette Frameworku, hledal jsem nějaký vhodný příklad, na kterém bych demonstroval zranitelnost webových aplika...',
  },
  {
    timestamp: 1394562840000,
    title: 'Sbohem NDBT, vítej Doctrine',
    slug: 'sbohem-ndbt-vitej-doctrine',
    preview:
      'Byl jsem požádán, abych napsal nejenom důvod přechodu z Nette Database na Doctrine, ale obecně co mě k tomu vedlo a jak takový přechod vlastně učinit....',
  },
  {
    timestamp: 1393877713000,
    title: 'AJAX upload souborů v Nette pomocí Fine Uploaderu',
    slug: 'ajax-upload-souboru-v-nette-pomoci-fine-uploaderu-2',
    preview:
      'Dříve jsem psal o tom, jak použít Fine Uploader jakožto nástroj pro AJAXové nahrávání souborů na server. Původní článek však platí pouze pro verzi 3.*...',
  },
  {
    timestamp: 1393588145000,
    title: 'Čteme QR kódy bez čtečky',
    slug: 'cteme-qr-kody-bez-ctecky',
    preview:
      '\nPatříte mezi lidi, kteří se nespokojí pouze se čtečkou QR kódů, ale chcete vědět jak fungují? Nebo co víc jak je přečíst bez použité takové čtečky? T...',
  },
  {
    timestamp: 1393074151000,
    title: 'Omyly hashování hesel',
    slug: 'omyly-hashovani-hesel',
    preview:
      'Někdy minulý rok jsem si četl prezentaci Michala Špačka .{target:_blank} o hashování hesel .{target:_blank} a byl jsem z toho poněkud zklamán. Naprost...',
  },
  {
    timestamp: 1392999129000,
    title: 'Jaký email je nejvíce využívaný?',
    slug: 'jaky-email-je-nejvice-vyuzivany',
    preview:
      'Včera jsem psal o bezpečnostní chybě, která umožňuje získat podle mého názoru nezanedbatelně velký vzorek emailových adres. Krom toho, že bych byl rád...',
  },
  {
    timestamp: 1392936502000,
    title: 'Kde se berou spamy?',
    slug: 'kde-se-berou-spamy',
    preview:
      'Tento článek navazuje na článek Stáhněte si zdarma 897457 emailových adres z ledna tohoto roku. Přečtěte si jej prosím, ať víte o co jde.\n\nRád bych tí...',
  },
  {
    timestamp: 1391460655000,
    title: 'Udržujete dokumentaci stále aktuální?',
    slug: 'udrzujete-dokumentaci-stale-aktualni',
    preview:
      'Již dlouho si v hlavě pohrávám s jednou myšlenkou, kterou stále nemohu dovést do zdárného konce. Již na samém začátku jsem již však věděl, že se zajis...',
  },
  {
    timestamp: 1391334426000,
    title: 'Použití Texy s FSHL',
    slug: 'pouziti-texy-s-fshl',
    preview:
      'Někdy (hodně dávno) jsem kdesi našel poměrně hezký a jednoduchý postup jak implementovat Texy .{target:_blank} s použitím FSHL .{target:_blank} na web...',
  },
  {
    timestamp: 1391288978000,
    title: 'Vlna na webu',
    slug: 'vlna-na-webu',
    preview:
      'Vlna je program Petra Olšáka .{target:_blank}, který slouží k umístění nezalomitelné místo na místo v textu, kde by nemělo dojít k samovolnému zalomen...',
  },
  {
    timestamp: 1390929034000,
    title: 'Představení projektu Vacuum - STATIC',
    slug: 'predstaveni-projektu-vacuum-static',
    preview:
      'Vzhledem k tomu, že vzrostl zájem o Vacuum projekty, rozhodl jsem se zde uveřejnit postup jak pracovat s projektem Vacuum - STATIC (https://bitbucket....',
  },
  {
    timestamp: 1390822437000,
    title: 'Stáhněte si zdarma 897457 emailových adres',
    slug: 'stahnete-si-zdarma-897457-emailovych-adres',
    preview:
      'V následujícím článku bych rád nastínil problematiku newsletterů nejen z programátorského hlediska a také bych se rád opřel do bezpečnosti společnosti...',
  },
  {
    timestamp: 1387710504000,
    title: 'Veřejná distribuce klíčů',
    slug: 'verejna-distribuce-klicu',
    preview:
      '\n  Inspirací a zdrojem informací pro tento článek byla kniha Simona Singha - Kniha kódu a šifer.\n  Utajování od starého Egypta po kvantovou kryptograf...',
  },
  {
    timestamp: 1387385662000,
    title: 'Přednáška z Nette na ZČU',
    slug: 'prednaska-z-nette-na-zcu',
    preview:
      'Dnes jsem měl tu čest přednášet na ZČU studentům předmětu KIV/WEB - Webové aplikace.\nPřednášku a i celé povídání jsem se snažil chopit velmi realistic...',
  },
  {
    timestamp: 1387113023000,
    title: 'Nette 2.2-dev',
    slug: 'nette-2-2-dev',
    preview:
      'Nedávno byla změněna vývojová verze Nette Frameworku na 2.2-dev (https://github.com/nette/nette/commit/3a426255084163ec1a2f324ea0d3e9b3139adccc).\nTato...',
  },
  {
    timestamp: 1382905620000,
    title: 'Změna URL struktury',
    slug: 'zmena-url-struktury',
    preview:
      'Rád bych tímto upozornil na změny URL adres na tomto webu. A zároveň k technické povaze tohoto webu\nprozradím i bližší informace ze zákulisí.\nPřed úpr...',
  },
  {
    timestamp: 1382391734000,
    title: 'LaTeX šablona',
    slug: 'latex-sablona',
    preview:
      'Všiml si také někdo, že většina uživatelů WYSIWYG textového editoru typu Word v něm menšinu času píší a většinu času se snaží ohnout editor tak, aby d...',
  },
  {
    timestamp: 1377116084000,
    title: 'AJAX upload souborů v Nette pomocí Fine Uploaderu',
    slug: 'ajax-upload-souboru-v-nette-pomoci-fine-uploaderu',
    preview:
      'Následující text řeší starší verzi FineUploaderu 3.*, nikoliv nejnovější. Hledáte-li aktuálnější návod, přečtěte si prosím http://zlml.cz/ajax-upload-...',
  },
  {
    timestamp: 1376169022000,
    title: 'RSS a Sitemap jednoduše a rychle',
    slug: 'rss-a-sitemap-jednoduse-a-rychle',
    preview:
      'Pár článků zpět jsem ukazoval několik příkladů, jak tvořit různé routy. Ukazoval jsem routy pro RSS i sitemap.xml. Nikde jsem však zatím neukazoval ja...',
  },
  {
    timestamp: 1376166681000,
    title: 'Fluent interface a PCRE',
    slug: 'fluent-interface-a-pcre',
    preview:
      'Na následujících řádcích předvedu dvě věci. První je úžasný nápad jak vytvářet regulární výrazy pomocí fluent zápisu (inspirace .{target:_blank}), což...',
  },
  {
    timestamp: 1375611559000,
    title: 'Routování v Nette - prakticky',
    slug: 'routovani-v-nette-prakticky',
    preview:
      'Tento článek byl naposledy revidován, aktualizován a rozšířen 27. června 2014...\n\nV následujícím článku se budu opírat o teorii napsanou v dokumentaci...',
  },
  {
    timestamp: 1375215333000,
    title: 'Problémy fulltextu v Nette',
    slug: 'problemy-fulltextu-v-nette',
    preview:
      'Nedávno jsem psal o tom, jak využívat fulltext indexy na InnoDB tabulkách (http://zlml.cz/using-fulltext-searching-with-innodb).\nNení to nic převratné...',
  },
  {
    timestamp: 1375126671000,
    title: 'Using fulltext searching with InnoDB',
    slug: 'using-fulltext-searching-with-innodb',
    preview:
      'Sometimes is quite useful to use InnoDB engine. \nUnfortunately InnoDB is good for tables with foreign keys, but useless for fulltext search. \nYou can&...',
  },
  {
    timestamp: 1375087004000,
    title: 'Třída pro připojení k FIO API',
    slug: 'trida-pro-pripojeni-k-fio-api',
    preview:
      'Další užitečný úryvek, který je škoda nechat ležet v Git repozitářích.\nA opět uzpůsobený pro používání s Nette FW.\nNedávno jsem psal o tom, jak použív...',
  },
  {
    timestamp: 1375044812000,
    title: 'CRON validátor',
    slug: 'cron-validator',
    preview:
      'A jak už to tak bývá, tak opět ohnutý pro Nette. Tentokráte inspirovaný řešením ISPConfigu.\nMůžeš tohle, nesmíš tamto #Samotný CRON zápis je velmi roz...',
  },
  {
    timestamp: 1375043798000,
    title: 'Testování presenterů v Nette',
    slug: 'testovani-presenteru-v-nette',
    preview:
      'Tak toto je přesně to téma o kterém se naustále mluví, ale tím to z velké části končí.\nNemá smysl zabývat se tím, jestli testovat, nebo ne. Na to už s...',
  },
  {
    timestamp: 1363554156000,
    title: 'Nette 2.1-dev CliRouter',
    slug: 'nette-2-1-dev-clirouter',
    preview:
      'Routování CLI((Command Line Interface)) aplikací je oblast, o které se v Nette moc nemluví. A když mluví, tak divně (nebo staře). Což na jednu stranu ...',
  },
  {
    timestamp: 1356648759000,
    title: 'Návrhový vzor Factory Method',
    slug: 'navrhovy-vzor-factory-method',
    preview:
      'Návrhový vzor Factory Method má za úkol definovat rozhraní pro vytváření objektů s tím, že vlastní tvorbu instancí přenechává potomkům. Samotný návrho...',
  },
  {
    timestamp: 1356550681000,
    title: 'Návrhový vzor Singleton',
    slug: 'navrhovy-vzor-singleton',
    preview:
      'Návrhový vzor Singleton je velmi známý. Má za úkol zajistit, že bude z určité třídy existovat pouze jedna instance. K této instanci poskytne globální ...',
  },
  {
    timestamp: 1356472874000,
    title: 'Osm návrhových přikázání',
    slug: 'osm-navrhovych-prikazani',
    preview:
      'Právě mám rozečtenou knihu, která popisuje návrhové vzory v PHP. Mimo jiné autor popisuje pravidla při návrhu softwaru, která jsou prokládána velkým m...',
  },
  {
    timestamp: 1353707019000,
    title: 'Výpočet mediánu',
    slug: 'vypocet-medianu',
    preview:
      'Zadání #Najděte v dostupné literatuře nebo vymyslete co nejlepší algoritmus pro výpočet mediánu.\nNezapomeňte na citaci zdrojů. Kritéria kvality v sest...',
  },
  {
    timestamp: 1349557092000,
    title: 'Tabulkový masakr',
    slug: 'tabulkovy-masakr',
    preview:
      'Určitě znáte HTML a tím pádem znáte i tabulky. Pro jistotu připomenutí.\nTabulka se v HTML tvoří párovým tagem &lt;table&gt;&lt;/table&gt;, její řádky ...',
  },
  {
    timestamp: 1347738626000,
    title: 'Lovec matematik',
    slug: 'lovec-matematik',
    preview:
      'Znáte následující hádanku?\nLovec ráno vyrazí na lov. Nejprve jde 10 km na jih, poté 10 km na západ a nakonec 10 km na sever. V cíli své cesty zjišťuje...',
  },
  {
    timestamp: 1347049798000,
    title: 'Asymetrická šifra s veřejným klíčem',
    slug: 'asymetricka-sifra-s-verejnym-klicem',
    preview:
      'O veřejné distribuci klíčů jsem již dříve psal. Pojďme se však podívat nejen na samotnou distribuci, ale i na myšlenku asymetrického šifrování. Prvně ...',
  },
];

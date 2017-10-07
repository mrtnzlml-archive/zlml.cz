---
id: ec59389a-b2a2-4033-bbc6-f778efe7095a
timestamp: 1409163060000
title: Komunikace s ERP pomocí XML-RPC
slug: komunikace-s-erp-pomoci-xml-rpc
---
Spousta lidí by se ráda připojovala na API ERP systému [Odoo](https://www.odoo.com/), ne vždy je to však procházka růžovým sadem, protože se očekává místy až přehnaná interní znalost tohoto systému. Přitom je to zbytečné. V dnešním článku se pokusím zdokumentovat právě tuto žalostně zdokumentovanou stránku věci tak, aby to zvládl každý alespoň trochu zdatný programátor.

Prvně však několik málo slov o co vlastně jde. Odoo je ERP ([Enterprise Resource Planning](http://www.orgis.cz/sluzby/in-house)) systém poměrně bohatý na funkce a má za úkol řešit zejména vnitrofiremní procesy a obecně všechny záležitosti, které se okolo jakékoliv firmy motají. Lze tedy řešit plánování projektů, jejich workflow, rozvrhování času, řízení zakázek, ale také například správu dokumentů, skladové zásoby, mass mailing, nebo tzv. [Point of Sale](https://www.odoo.com/page/point-of-sale) což je jedna z těch nejvíce zajímavých věcí, alespoň z mého pohledu. Zjednodušeně řečeno lze udělat naprosto cokoliv. A co nejde, tak se jednoduše doprogramuje. Aby však šlo udělat cokoliv, je potřeba připojovat se na tento systém vzdáleně, protože občas se hodí propojit stávající webovou aplikaci právě s takovýmto systémem. To může mít několik důvodů. Například chcete mít ve webové aplikaci data sjednocená s ERP systémem, nebo si chcete vzdáleně stahovat faktury, popř. tlačit data do účtovacího systému. Jak již bylo řečeno - možné je naprosto cokoliv.

# Hello API!

Odoo poskytuje klasické XML-RPC API. Toto API je velmi jednoduché na obsluhu, nicméně ani tento druh API [není můj favorit](srackoapi). S výhodou tedy můžeme použít lehce modifikovanou funkci [Jakuba Vrány](http://php.vrana.cz/webove-sluzby-v-php-xml-rpc-a-soap.php) pro obsluhu tohoto API (PHP):

```php
<?php

function xmlrpc($url, $method, $params = array(), $types = array(), $encoding = 'utf-8') {
    foreach ($types as $key => $val) {
        xmlrpc_set_type($params[$key], $val);
    }
    $context = stream_context_create(array('http' => array(
        'method' => "POST",
        'header' => "Content-Type: text/xml",
        'content' => xmlrpc_encode_request($method, $params, array('encoding' => $encoding))
    )));
    return xmlrpc_decode(file_get_contents($url, false, $context), $encoding);
}
```

Následuje krátká odbočka k tomu, co vše je možné přes API udělat. Opět bych mohl napsat, že cokoliv, ale zde už si nejsem jist a proto následuje výpis funkcí, které je možné přes api volat a hlavně které považuji za důležité. Existují totiž 4 služby a každá obsahuje jiné metody. Nejhlavnější služba je `common`. Zde jsou k dispozici mimo jiné funkce `login(db, login, password)`, `about(extended=False)`, `timezone_get(db, login, password)` a `version()`. Většina funkcí se dostatečně popisuje sama, pozor však na funkci "about", protože ta v době psaní tohoto článku [obsahovala chybu](https://github.com/odoo/odoo/pull/2028). Je tedy zřejmé, že tato skupina funkcí se hodí pro zalogování do systému, nebo pro zjištění časového pásma, popř. verze systému. Pojďme se tedy přihlásit:

```php
<?php
//...
$data = array('database', 'username', 'password');
$uid = xmlrpc("http://.../xmlrpc/common", "login", $data);
```

Výsledkem volání je unikátní identifikátor uživatele, který si můžeme dočasně někam uložit, aby ho nebylo potřeba zjišťovat pořád znovu. To není potřeba. Nutné ja však upozornit na to, že přes API získáte taková přístupová práva, jaké by měl uživatel, kdyby se přihlašoval normálně pomocí loginu.

# Jedeme dál

Následuje služba `object`. Ta má na starost práci s databází z hlediska ORM. Ačkoliv má tato služba pouze dvě pro mě zajímavé funkce, užije se s ní nejvíce srandy a patří asi k té nejdůležitější. Zmiňované funkce jsou `execute(db, uid, obj, method, *args, **kw)` a `exec_workflow(db, uid, obj, signal, *args)`. Právě pomocí `execute` lze například vyhledávat v databázi a to tak, že si nejdříve získáme ID hodnoty pro daný výraz a následně si vytáhneme veškeré informace, které jsou potřeba (pokud jsou potřeba). Příklad pro vyhledávání v zákaznících:

```php
<?php
//...
$data = array($database, $uid, $password, 'res.partner', 'search', [['name', 'ilike', 'hledanyvyraz']]);
$ids = xmlrpc("http://.../xmlrpc/object", "execute", $data); //získáme IDčka

$data = array($database, $uid, $password, 'res.partner', 'read', $ids, ['image', 'display_name', 'function', 'email']);
$users = xmlrpc("http://.../xmlrpc/object", "execute", $data); //získáme zákazníky

//ukázka vytváření klienta:
$data = array($database, $uid, $password, 'res.partner', 'create', ['name' => 'John Doe']);
xmlrpc("http://.../xmlrpc/object", "execute", $data);
```

Je tedy zřejmé, že pomocí execute můžeme vyhledávat, číst, ale i vytvářet, nebo mazat záznamy (`create`, `search`, `read`, `write`, `unlink`). Zajímavý je způsob zápisu při hledání (<em>ilike</em>). K dispozici jsou následující operátory: `=`, `!=`, `>`, `>=`, `<`, `<=`, `like`, `ilike`, `in`, `not in`, `child_of`, `parent_left`, `parent_right`. Opět se jedná o "samosepopisující" názvy. Nejzajímavější je však právě <em>ilike</em>, který není case sensitive a obaluje dotaz procenty jako je tomu například klasicky v MySQL (`%hledanyvyraz%`). U použíté funkce <em>read</em> lze vyjmenovat jaké sloupce se mají vrátit, nebo se vrátí veškerá data (včetně obrázků v base64).

# Pokročilé dotazování

Podmínky dotazování lze ještě zpřesnit pomocí logických operátorů (`&` - and, default, `|` - or, `!` - not). Podmínky se zapisují klasicky prefixově, takže pokud chceme například vyhledat zákazníka s nenastavenou češtinou z čech a německa, položíme například následující prefixový dotaz:

```python
[('name','=','Adam'),'!',('language.code','=','cs_CZ'),'](',('country_id.code','=','CZ'),('country_id.code','=','DE'))
# ekvivalentní k:
[('name','=','Adam'),('language.code','!=','cs_CZ'),'](',('country_id.code','=','CZ'),('country_id.code','=','DE'))
```

To v jakých modulech (<em>res.partner</em>) se bude vyhledávat právě záleží na tom, co je naintalováno za moduly a je třeba vědět jak jsou interně označeny, což je podle mého nešťastné, ale zde je přehled těch nejčastějších

- Zákazníci: `res.partner`
- Telefonáty: `crm.phonecall`, obch. příležitosti - `crm.lead`
- Produkty: `product.template`, kategorie produktů - `product.category`
- Projekty: `project.project`, úkoly - `project.task`, tagy - `project.category`
- Faktury: `account.invoice`, smlouvy - `account.analytic.account`
- Zaměstnanci: `hr.employee`, výdaje - `hr.expense.expense`
- Znalostní báze: `document.directory`
- Kalendář: `calendar.event`

Je svělé, že stačilo pár ukázek a s celým ERP lze dělat téměř cokoliv. Tím to však nekončí, protože existují ještě dvě skupiny příkazů. Třetí skupinou je skupina pro obsluhu a generování reportů `report`. Tato skupina mi v současné chvíli nepřijde natolik zajímavá, ale jmenovitě se jedná o funkce `report`, `report_get` a `render_report`. Mnohem zajímavější, ačkoliv možná trošku vyšší dívčí, je skupina pro přímou práci s databází ERP. Jedná se o skupinu `db` a ta ovládá právě instance celého ERP. Tyto funkce nejsou přímo pro zákazníky, ani obsluhu ERP, ale spíše pro administrátory serverů, tzn. je nutné autorizovat se master helem. Databáze lze mazat, vytvářet, přesouvat, zálohovat, migrovat, duplikovat atd. viz následující výčet funkcí: `create`, `drop`, `dump`, `restore`, `rename`, `change_admin_password`, `migrate_databases`, `create_database` a `duplicate_database`. Zejména pak třeba <em>rename</em> se hodí, protože tato operace nejde jinak vykonat. U všech zmíněných funkcí je nutné se ověřit. U následujících informativních to nuté není: `db_exist`, `list`, `list_lang`, `server_version`. Velký pozor na funkci <em>db_exist</em>. Ve skutečnosti totiž kontroluje, jestli se lze k databázi připojit, nikoliv jestli existuje, což nutně nemusí být jedno a to samé...

Pokud si chcete o dané problematice přečíst více, doporučuji tuto zastaralou [dokumentaci](https://doc.odoo.com/6.1/developer/12_api/). Je dobrá, nikoliv však postačující. Obsahuje však ukázky i pro jiné programovací jazyky, než je PHP. Doufám, že tento článek vyjasnil všechny zákoutí komunikace s API ERP systému Odoo... (-:
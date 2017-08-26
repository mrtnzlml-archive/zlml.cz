---
id: 17782730-319b-40ea-92d0-e33c84b32e29
timestamp: 1375215333000
title: Problémy fulltextu v Nette
slug: problemy-fulltextu-v-nette
---
Nedávno jsem psal o tom, jak využívat fulltext indexy na InnoDB tabulkách (http://zlml.cz/using-fulltext-searching-with-innodb).
Není to nic převratného, ale každý den se to také nedělá. Zmínil jsem také, jak vyhledávat, což
bylo řešení [Jakuba Vrány .{target:_blank}](http://php.vrana.cz/fulltextove-vyhledavani-v-mysql.php).

V diskusi pod článkem zmíňka o tom, jak ošetřit případ, kdy databáze nevrací výsledky pro slova,
která jsou kratší než je hodnota **ft_min_word_len**. Implementace pro Nette nebude nijak zvlášť
rozdílná, avšak i zde existuje minimálně jedna zrádnost.

# Nette, ty jedna zrádná bestie...


Pro dotazování využívám databázovou vrstvu \Nette\Database, což je rozšíření (nadstavba) pro PDO.
S oblibou také využívám fluid zápis a spoléhám na automatické ošetření vstupů. A zde je právě kámen úrazu.
Nette je místy až příliš důkladné (což je dobře), ale v tomto případě to znemožňuje korektní
použití **REGEXP**.

Běžný kód pro fultextové dotazování může vypadat takto:

```php
/** @var Nette\Database\SelectionFactory @inject */
public $sf;
    
$this->sf->table('mirror_posts')
	->where("MATCH(title, body) AGAINST (? IN BOOLEAN MODE)", $search)
	->order("5 * MATCH(title) AGAINST (?) + MATCH(body) AGAINST (?) DESC", $search, $search)
	->limit(50);
```

Což vygeneruje přibližně přesně následující:

```sql
SELECT `id`, `title`, `body` 
FROM `mirror_posts` 
WHERE (MATCH(`title`, `body`) AGAINST ('api' IN BOOLEAN MODE)) 
ORDER BY 5 * MATCH(`title`) AGAINST ('api') + MATCH(`body`) AGAINST ('api') DESC 
LIMIT 50
```

Bohužel tento dotaz nevrátí nic. Je to právě kvůli hodnotě **ft_min_word_len**, kterou mám nastavenou
na 4. Takže můžu změnit tuto hodnotu, a nebo pro všechny slova, která jsou kratší než 4 znaky
poskládám složitější dotaz:

```php
$where = "";
//$ft_min_word_len = mysql_result(mysql_query("SHOW VARIABLES LIKE 'ft_min_word_len'"), 0, 1);
$ft_min_word_len = 4;
preg_match_all("~[\\pL\\pN_]+('[\\pL\\pN_]+)*~u", stripslashes($search), $matches);
foreach ($matches[0] as $part) {
	if (iconv_strlen($part, "utf-8") < $ft_min_word_len) {
		$regexp = "REGEXP '[[:<:]]" . addslashes($part) . "[[:>:]]'";
		$where .= " OR (title $regexp OR body $regexp)";
	}
}
```

A doplníme fluidní dotaz:

```php
...
->where("MATCH(title, body) AGAINST (? IN BOOLEAN MODE)$where", $search) //přidáno $where
...
```

Nyní budu vyhledávat stejný výraz a to automaticky poskládaným dotazem:

```sql
SELECT `id` 
FROM `mirror_posts` 
WHERE (MATCH(`title`, `body`) AGAINST ('api' IN BOOLEAN MODE) OR (`title` REGEXP '[[:<:]]`api`[[:>:]]' OR `body` REGEXP '[[:<:]]`api`[[:>:]]')) 
ORDER BY 5 * MATCH(`title`) AGAINST ('api') + MATCH(`body`) AGAINST ('api') DESC 
LIMIT 50
```

Bohužel, ani tento dotaz nevrátí strávný výsledek, ačkoliv se tváří, že by měl.
Důvodem jsou zpětné uvozovky v regulárním výrazu **''[[:<:]]`api`[[:>:]]''**.

Řešení je zřejmě několik. Například poskládat si tento dotaz sám. Ovšem to není ta nejbezpečnější cesta.
Escapování je zrádné a zrovna vyhledávání je jedna z nejvíce používaných věcí, kdy se uživatel
přímo ptá databáze. Existuje však vyčůranější způsob.

Co jsem tak vypozoroval, tak Nette se sice o escapování snaží, ale neescapuje výraz zapsaný pomocí
<em>strtoupper()</em>. Tzn. že stačí změnit tvorbu výrazu:

```php
$regexp = "REGEXP '[[:<:]]" . addslashes(strtoupper($part)) . "[[:>:]]'";
```

A dotaz se následně poskládá strávně:

```sql
SELECT `id`, `title`, `body` 
FROM `mirror_posts` 
WHERE (MATCH(`title`, `body`) AGAINST ('api' IN BOOLEAN MODE) OR (`title` REGEXP '[[:<:]]API[[:>:]]' OR `body` REGEXP '[[:<:]]API[[:>:]]')) 
ORDER BY 5 * MATCH(`title`) AGAINST ('api') + MATCH(`body`) AGAINST ('api') DESC 
LIMIT 50
```

To že je část výrazu jiná než ve skutečnosti nevadí. Nevím jestli je case-insensitive chování
vlastnost REGEXP, ale tabulkou s postfixem **_ci** se také nic nezkazí.

Jen mě tak napadá, proč se to chová tak zvláštně. Uspokojuji se tím, že zpětná uvozovka
není úplně součástí escapování, takže se není čeho bát (a první regulár v PHP také nepustí vše),
ale je to divné.
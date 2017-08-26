---
id: 16cdfe07-444b-48ee-ae7a-ff85893edba5
timestamp: 1376166681000
title: Fluent interface a PCRE
slug: fluent-interface-a-pcre
---
Na následujících řádcích předvedu dvě věci. První je úžasný nápad jak vytvářet regulární výrazy pomocí fluent zápisu ([inspirace .{target:_blank}](https://github.com/VerbalExpressions/PHPVerbalExpressions/blob/master/VerbalExpressions.php)), což je druhá věc o které bych se rád zmínil.

# Regulární výrazy jsou peklo

Ačkoliv znám pár lidí, které regulární výrazy umí, je jich opravdu pár. A nikdo z nich o sobě neřekne, že je umí. Následuje příklad velmi triviálního výrazu, který je ovšem dosti špatný, což je dobře, protože se k tomu vrátím později:

```
/^(http)(s)?(\:\/\/)(www\.)?([^ ]*)(\.)([^ ]*)(\/)?$/
```

Tento výraz akceptuje přibližně tvar URL. Je však zřejmé, že je to zápis, který je nesmírně náročný na vymyšlení a extrémně náchylný ke tvoření chyb. Proto je vhodné si jeho tvorbu zjednodušit například nějakou třídou:

```php
<?php

class Regexp {

	private $regexp = '';

	public function has($value) {
		$this->regexp .= "(" . preg_quote($value, '/') . ")";
		//return $this;   -   potřebné pro fluent interface
	}

	public function maybe($value) {
		$this->regexp .= "(" . preg_quote($value, '/') . ")?";
		//return $this;   -   potřebné pro fluent interface
	}

	public function anythingBut($value) {
		$this->regexp .= "([^" . preg_quote($value, '/') . "]*)";
		//return $this;   -   potřebné pro fluent interface
	}

	public function __toString() {
		return "/^$this->regexp$/";
	}

}
```

S tím, že její použití je prosté:

```php
$regexp = new Regexp();
$regexp->then('http');
$regexp->maybe('s');
$regexp->then('://');
$regexp->maybe('www.');
$regexp->anythingBut(' ');
$regexp->then('.');
$regexp->anythingBut(' ');
$regexp->maybe('/');
echo $regexp . '<br>';
echo preg_match($regexp, 'http://zlml.cz/') ? 'P' : 'F';
echo preg_match($regexp, 'https://zlml.cz/') ? 'P' : 'F';
```

Nemusím však říkat, že to minimálně vypadá naprosto otřesně. Spousta psaní, až moc objektové chování. Elegantnější řešení přináší právě fluent interface.

# Fluent interfaces, regulární peklo chladne

Fluent interface je způsob jak řetězit metody za sebe. Používá se poměrně často, ušetří spoustu zbytečného psaní a velmi prospívá srozumitelnosti kódu. Nevýhodou je, že se musí v každé metodě vrátit objekt <code>return $this;</code>, na což se nesmí zapomenout. Každopádně výsledek je skvostný:

```php
$regexp = new Regexp();
$regexp->then('http')
		->maybe('s')
		->then('://')
		->maybe('www.')
		->anythingBut(' ')
		->then('.')
		->anythingBut(' ')
		->maybe('/');
echo $regexp . '<br>';
echo preg_match($regexp, 'http://zlml.cz/') ? 'P' : 'F';
echo preg_match($regexp, 'https://zlml.cz/') ? 'P' : 'F';
```

Teprve zde vynikne to, jak je důležité správně (čti stručně a jasně) pojmenovávat metody. Díky fluent interfaces lze programovat téměř ve větách, které jsou naprosto srozumitelné.

# Ne, peklo je opět peklem

Ačkoliv by se mohlo zdát, že díky objektu, který pomáhá tvořit regulární výrazy je jejich kompozice jednoduchou záležitostí, není tomu tak. Vrátím se k původnímu výrazu, který není dobrý. Proč? V reálném světě je kontrola, resp. předpis, který musí daná adresa mít daleko složitější. Například <code>http</code> nemusí být vůbec přítomno, pokud však je, musí následovat možná <code>s</code> a zcela určitě <code>://</code>. To samé s doménou. Ta může být jen určitý počet znaků dlouhá, může obsahovat tečky (ale ne neomezené množství), samotná TLD má také určitá pravidla (minimálně co se týče délky) a to nemluvím o parametrech za adresou, které jsou téměř bez limitu.

Zkuste si takový objekt napsat. Ve výsledku se i nadále budou regulární výrazy psát ručně, nebo se ve složitějších případech vůbec používat nebudou.
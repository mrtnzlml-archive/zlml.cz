---
id: bd6cef8f-9b0a-48ff-a700-2aa32d89a32b
timestamp: 1391334426000
title: Použití Texy s FSHL
slug: pouziti-texy-s-fshl
---
Někdy (hodně dávno) jsem kdesi našel poměrně hezký a jednoduchý postup jak implementovat [Texy .{target:_blank}](http://texy.info/) s použitím [FSHL .{target:_blank}](http://fshl.kukulich.cz/) na webu. Rád bych se zde podělil o postup, který používám již na řadě projektů, které potřebují zvýrazňování syntaxe.

# Použití samotného Texy

Běžně by se Texy zaregistrovalo do šablony jako helper:

```php
/**
 * @param null $class
 * @return Nette\Templating\ITemplate
 */
protected function createTemplate($class = NULL) {
	$template = parent::createTemplate($class);
	$texy = new \Texy();
	$template->registerHelper('texy', callback($texy, 'process'));
	return $template;
}
```

Tento helper lze i nadále používat. Hodě se například pokud je potřeba Texy prvky naopak escapovat:

```
{$post->body|texy|striptags}
```

# Použití Texy s FSHL

Samotné texy je sice geniální nástroj. Pro samotné zpracování se zvýrazněním se však hodí funkcionalitu Texy rozšířit, jelikož je potřeba zpracovat vstupující text a ty správná místa prohnat také tím správným lexxerem ve FSHL. K tomu dobře poslouží následující třída, která dědí právě od Texy:

```php
<?php

class fshlTexy extends Texy {

	public function blockHandler($invocation, $blocktype, $content, $lang, $modifier) {
		if ($blocktype !== 'block/code') {
			return $invocation->proceed(); //vstup se nebude zpracovavat
		}

		$highlighter = new \FSHL\Highlighter(
			new \FSHL\Output\Html(),
			\FSHL\Highlighter::OPTION_TAB_INDENT | \FSHL\Highlighter::OPTION_LINE_COUNTER
		);

		$texy = $invocation->getTexy();
		$content = Texy::outdent($content);

		//Set correct lexer:
		switch(strtoupper($lang)) {
			case 'CPP': $lexer = new \FSHL\Lexer\Cpp(); break;
			case 'CSS': $lexer = new \FSHL\Lexer\Css(); break;
			case 'HTML': $lexer = new \FSHL\Lexer\Html(); break;
			case 'JAVA': $lexer = new \FSHL\Lexer\Java(); break;
			case 'JAVASCRIPT': $lexer = new \FSHL\Lexer\Javascript(); break;
			case 'NEON': $lexer = new \FSHL\Lexer\Neon(); break;
			case 'PHP': $lexer = new \FSHL\Lexer\Php(); break;
			case 'PYTHON': $lexer = new \FSHL\Lexer\Python(); break;
			case 'SQL': $lexer = new \FSHL\Lexer\Sql(); break;
			case 'TEX': $lexer = new \FSHL\Lexer\Tex(); break; //WARNING: vlastní výroba!
			case 'TEXY': $lexer = new \FSHL\Lexer\Texy(); break;
			default: $lexer = new \FSHL\Lexer\Minimal();
		}

		$content = $highlighter->highlight($content, $lexer);
		$content = $texy->protect($content, Texy::CONTENT_BLOCK);

		$elPre = TexyHtml::el('pre');
		if ($modifier) {
			$modifier->decorate($texy, $elPre);
		}
		$elPre->attrs['class'] = strtolower($lang);

		$elCode = $elPre->create('code', $content);

		return $elPre;
	}

}
```

Tato třída při správném použití zajistí, že se použije ten správný lexer a ještě na úrovni PHP zajistí změnu výstupu. Konkrétně obalí určitá klíčová slova (v závislosti na kontextu) tagem <code>&lt;span&gt;</code> se zvláštní třídou. Toho se následně lze chytit v CSS a HTML výstup obarvit. Použití této třídy například v metodě <code>render*()</code>:

```php
$texy = new \fshlTexy();
//registrace handleru z nové třídy:
$texy->addHandler('block', array($texy, 'blockHandler'));
//dále stejně jako klasické použití Texy:
$texy->tabWidth = 4;
$texy->headingModule->top = 3; //start at H3
$this->template->body = $texy->process($post->body);
```

Výstup lze pak v Latte lehce podchytit a zobrazit:

```
{$body|noescape}
```

# Barvy, barvy, barvičky

Programově je sice tělo dokumentu vypsáno s tagy <code>&lt;span&gt;</code> s příslušnou třídou. To se však nijak viditelně neprojeví. Celou krásu udělá teprve CSS. Lze použít výchozí hodoty FSHL a vložit je do vlastního souboru stylů:

```css
/* Common */
.xlang { color: #ff0000; font-weight: bold; }
.line { color: #888888; background-color: #ffffff; }

/* CSS */
.css-at-rule { color: #004a80; font-weight: bold; }
.css-tag { color: #004a80; }
.css-id { color: #7da7d9; font-weight: bold; }
.css-class { color: #004a80; }
.css-pseudo { color: #004a80; }
.css-property { color: #003663; font-weight: bold; }
.css-value { color: #448ccb; }
.css-func { color: #448ccb; font-weight: bold; }
.css-color { color: #0076a3; }
.css-comment { background-color: #e5f8ff; color: #999999; }

/* CPP */
.cpp-keywords1 {color: #0000ff; font-weight: bold;}
.cpp-num {color: #ff0000;}
.cpp-quote {color: #a52a2a; font-weight: bold;}
.cpp-comment {color: #00ff00;}
.cpp-preproc {color: #c0c0c0;}

/* HTML */
.html-tag {color: #598527; font-weight: bold;}
.html-tagin {color: #89a315}
.html-quote {color: #598527; font-weight: bold;}
.html-comment {color: #999999; background-color: #f1fae4;}
.html-entity {color: #89a315;}

/* Java */
.java-keywords1 {color: #0000ff; font-weight: bold;}
.java-num {color: #ff0000;}
.java-quote {color: #a52a2a; font-weight: bold;}
.java-comment {color: #009900;}
.java-preproc {color: #c0c0c0;}

/* Javascript */
.js-out {color: #898993;}
.js-keywords1 {color: #575757; font-weight: bold;}
.js-num {color: #575757;}
.js-quote {color: #575757; font-weight: bold;}
.js-comment {color: #898993; background-color: #f4f4f4;}

/* Neon */
.neon-section {color: #598527;}
.neon-sep {color: #ff0000;}
.neon-key {color: #0000ff;}
.neon-comment {color: #999999;}
.neon-value {color: #000000;}
.neon-quote {color: #884433;}
.neon-num {color: #448ccb;}
.neon-var {color: #ffaa00;}
.neon-ref {color: #884433;}

/* PHP */
.php-keyword1 {color: #dd2244; font-weight: bold;}
.php-keyword2 {color: #dd2244;}
.php-var {color: #ffaa00; font-weight: bold;}
.php-num {color: #ff0000;}
.php-quote {color: #884433; font-weight: bold;}
.php-comment {color: #999999; background-color: #ffffee;}

/* Python */
.py-keyword1 {color: #0033cc; font-weight: bold;}
.py-keyword2 {color: #ce3333; font-weight: bold;}
.py-keyword3 {color: #660066; font-weight: bold;}
.py-num {color: #993300;}
.py-docstring {color: #e86a18;}
.py-quote {color: #878787; font-weight: bold;}
.py-comment {color: #009900; font-style: italic;}

/* SQL */
.sql-keyword1 {color: #dd0000; font-weight: bold;}
.sql-keyword2 {color: #dd2222;}
.sql-keyword3 {color: #0000ff; font-weight: bold;}
.sql-value {color: #5674b9;}
.sql-comment {color: #ffaa00;}
.sql-num {color: #ff0000;}
.sql-option {color: #004a80; font-weight: bold;}

/* Tex */
.tex-func {color: #ffaa00; font-weight: bold;}
.tex-comment {color: #999999; background-color: #ffffee;}
.tex-attr1 {color: #dd2222;}
.tex-attr2 {color: #0000ff; font-weight: bold;}
.tex-math {color: #00AA00; font-weight: bold;}

/* Texy */
.texy-hlead {color: #4444bb; font-weight: bold;}
.texy-hbody {background-color: #eeeeff; color: #4444bb;}
.texy-hr {color: #bb4444;}
.texy-code {color: #666666;}
.texy-html {color: #66aa66;}
.texy-text {color: #6666aa;}
.texy-err {background-color: #ff0000; color: #ffffff;}
```

Celá krása tohoto řešení spočívá v tom, že nepoužívám žádné javascriptové knihovny, ale vše se provede pěkně na úrovni PHP a bude to tedy fungovat vždy, stejně tak jako Texy... (-:
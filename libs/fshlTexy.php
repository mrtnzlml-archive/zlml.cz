<?php

class fshlTexy extends \Texy\Texy {

	public function blockHandler($invocation, $blocktype, $content, $lang, $modifier) {
		if ($blocktype !== 'block/code') {
			return $invocation->proceed(); //vstup se nebude zpracovavat
		}

		$highlighter = new \FSHL\Highlighter(
			new \FSHL\Output\HtmlManual(),
			\FSHL\Highlighter::OPTION_TAB_INDENT
		);

		$texy = $invocation->getTexy();
		$content = \Texy\Texy::outdent($content);

		//Set correct lexer:
		switch (strtoupper($lang)) {
			case 'C':
			case 'CPP':
				$lexer = new \FSHL\Lexer\Cpp();
				break;
			case 'CSS':
				$lexer = new \FSHL\Lexer\Css();
				break;
			case 'HTML':
				$lexer = new \FSHL\Lexer\Html();
				break;
			//HtmlOnly lexer
			case 'JAVA':
				$lexer = new \FSHL\Lexer\Java();
				break;
			case 'JS':
			case 'JAVASCRIPT':
				$lexer = new \FSHL\Lexer\Javascript();
				break;
			case 'NEON':
				$lexer = new \FSHL\Lexer\Neon();
				break;
			case 'PHP':
				$lexer = new \FSHL\Lexer\Php();
				break;
			case 'PYTHON':
				$lexer = new \FSHL\Lexer\Python();
				break;
			case 'SQL':
				$lexer = new \FSHL\Lexer\Sql();
				break;
			case 'TEX':
				$lexer = new \FSHL\Lexer\Tex();
				break;
			case 'TEXY':
				$lexer = new \FSHL\Lexer\Texy();
				break;
			default:
				$lexer = new \FSHL\Lexer\Minimal();
		}

		$content = $highlighter->highlight($content, $lexer);
		$content = $texy->protect($content, \Texy\Texy::CONTENT_BLOCK);

		$elPre = \Texy\HtmlElement::el('pre');
		if ($modifier) {
			$modifier->decorate($texy, $elPre);
		}
		$elPre->attrs['class'] = strtolower($lang);
		$elPre->create('code', $content);
		return $elPre;
	}

}

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
			//HtmlOnly lexer
			case 'JAVA': $lexer = new \FSHL\Lexer\Java(); break;
			//Javascript
			//Minimal
			case 'PHP': $lexer = new \FSHL\Lexer\Php(); break;
			case 'PYTHON': $lexer = new \FSHL\Lexer\Python(); break;
			case 'SQL': $lexer = new \FSHL\Lexer\Sql(); break;
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
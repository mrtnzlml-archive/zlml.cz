<?php

namespace Zeminem;

use Nette\Utils\Html;
use WebLoader\Nette\WebLoader;

/**
 * Class JavaScriptLoader
 * @package Zeminem
 */
class JavaScriptLoader extends WebLoader {

	/**
	 * Get script element
	 * @param string $source
	 * @return Html
	 */
	public function getElement($source) {
		return Html::el("script")
			->addAttributes(['async' => 'async'])
			->type("text/javascript")
			->src($source);
	}

}
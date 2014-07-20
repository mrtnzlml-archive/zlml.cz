<?php

use Nette\Utils\Html;

class PictureExtension extends Nette\DI\CompilerExtension implements IMenuProvider, IPageProvider, IPresenterMappingProvider {

	public static function getMenuItems() {
		//použít latte šablony (?)
		$a = Html::el('a class="list-group-item"')->href('http://localhost.dev/zeminem.cz/www/admin/test');
		$h4 = Html::el('h4 class="list-group-item-heading"')->setText('Název menu položky');
		$p = Html::el('p class="list-group-item-text"')->setText('Popis menu položky');
		return $a->add($h4)->add($p);
	}

	public static function getPage() {
		return 'extensions'; //it's just for test
	}

	public static function getPresenterMapping() {
		return array('Picture' => 'App\\Extensions\\PictureExtension\\*Module\\*Presenter');
	}

}

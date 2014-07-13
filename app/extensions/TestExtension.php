<?php

use Nette\Utils\Html;

class TestExtension extends Nette\DI\CompilerExtension {

	public function afterCompile(Nette\PhpGenerator\ClassType $class) {
		$initialize = $class->methods['initialize'];
		$initialize->addBody('$stack = \Stack::getStack();');
		$initialize->addBody('$stack->addTemplate("extensions");'); //nejen umístění, ale i view, aby šlo extension úplně vypnout
		$initialize->addBody('$stack->addMenu(\TestExtension::getMenu());');
		//TODO: zaregistrovat a spustit další fíčury
	}

	public static function getMenu() {
		//použít latte šablony (?)
		$a = Html::el('a class="list-group-item"')->href('http://localhost.dev/zeminem.cz/www/admin/test');
		$h4 = Html::el('h4 class="list-group-item-heading"')->setText('Název menu položky');
		$p = Html::el('p class="list-group-item-text"')->setText('Popis menu položky');
		return $a->add($h4)->add($p);
	}

}

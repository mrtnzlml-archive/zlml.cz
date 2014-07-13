<?php

use Nette\Utils\Html;

class CoreExtension extends Nette\DI\CompilerExtension {


	public function afterCompile(Nette\PhpGenerator\ClassType $class) {
		$extensions = $this->compiler->getExtensions();
		$initialize = $class->methods['initialize'];
		$initialize->addBody('$stack = \Stack::getStack();');
		foreach ($extensions as $extension) {
			if ($extension instanceof IMenuProvider) {
				$initialize->addBody('$stack->addMenu(' . get_class($extension) . '::getMenuItems());');
			}
			if ($extension instanceof IPageProvider) {
				$initialize->addBody('$stack->addTemplate(' . get_class($extension) . '::getPage());');
			}
		}
	}

}

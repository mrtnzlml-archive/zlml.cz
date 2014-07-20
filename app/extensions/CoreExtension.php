<?php

use Nette\Utils\Html;

class CoreExtension extends Nette\DI\CompilerExtension {

	/*public function loadConfiguration() {
		$builder = $this->getContainerBuilder();
		$presenterFactory = $builder->getDefinition('nette.presenterFactory');
		foreach ($this->compiler->getExtensions() as $extension) {
			if ($extension instanceof IPresenterMappingProvider) {
				$presenterFactory->addSetup('setMapping', array($extension::getPresenterMapping()));
			}
		}
	}*/

	public function afterCompile(Nette\PhpGenerator\ClassType $class) {
		$initialize = $class->methods['initialize'];
		$initialize->addBody('$stack = \Stack::getStack();');
		foreach ($this->compiler->getExtensions() as $extension) {
			if ($extension instanceof IMenuProvider) {
				$initialize->addBody('$stack->addMenu(' . get_class($extension) . '::getMenuItems());');
			}
			if ($extension instanceof IPageProvider) {
				$initialize->addBody('$stack->addTemplate(' . get_class($extension) . '::getPage());');
			}
		}
	}

}

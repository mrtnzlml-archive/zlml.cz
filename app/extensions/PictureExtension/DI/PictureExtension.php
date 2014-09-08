<?php

use Nette\Application\Routers\Route;
use Nette\DI;
use Nette\PhpGenerator\PhpLiteral;

class PictureExtension extends Nette\DI\CompilerExtension {

	//TODO: instalace do databáze

	public function beforeCompile() {
		$builder = $this->getContainerBuilder();
		$router = $builder->getDefinition('router'); //TODO: route prepend, nebo $router[999] (nejde)
		$router->addSetup('offsetSet', array(new PhpLiteral('NULL'), new Route('newadmin/pictures[/<id>]', 'App:Pictures:default')));
		$builder->getDefinition('nette.presenterFactory')->addSetup('setMapping', array(array('App' => 'App\\*Module\\*Presenter')));
		//Admin Menu:
		$priorityQueue = new \SplPriorityQueue();
		$priorityQueue->insert(_('Obrázky'), 10);
		$adminMenuFactory = $builder->getDefinition('adminMenuFactory');
		foreach ($priorityQueue as $entry) {
			$adminMenuFactory->addSetup('addContributor', [$entry]);
		}
	}

}

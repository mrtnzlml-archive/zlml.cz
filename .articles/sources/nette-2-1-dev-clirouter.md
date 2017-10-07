---
id: 01c2866b-fd82-4562-ab63-dbab3505ee8e
timestamp: 1363554156000
title: Nette 2.1-dev CliRouter
slug: nette-2-1-dev-clirouter
---
Routování CLI((Command Line Interface)) aplikací je oblast, o které se v Nette moc nemluví. A když mluví, tak divně (nebo staře). Což na jednu stranu dává smysl, protože tato routa existuje už od roku 2009. Na druhou stranu je to zvláštní, protože je stále experimentální.

> The unidirectional router for CLI.
> 
> (experimental)

Dokonce se už mluvilo o tom, že se zruší. No snad se to nestane...

Proč o tom mluvím? Rád bych ukázal, jak se dá v nastávající verzi Nette tato routa použít. V nové verzi Nette se již routy nepíší do bootsrap.php jak tomu bylo (alespoň myslím) dříve. Tentokrát je v adresářové struktuře soubor router/**RouterFactory.php**, který se právě o routování postará. Tento soubor může vypadat například takto:

```php
<?php

namespace App;

use Nette,
	Nette\Application\Routers\RouteList,
	Nette\Application\Routers\Route,
	Nette\Application\Routers\CliRouter;

/**
 * Router factory.
 */
class RouterFactory {

	private $container;

	public function __construct(Nette\DI\Container $container) {
		$this->container = $container;
	}

	/**
	 * @return \Nette\Application\IRouter
	 */
	public function createRouter() {
		$router = new RouteList();
		if ($this->container->parameters['consoleMode']) {
			$router[] = new CliRouter(array('action' => 'Cli:Cli:cron'));
		} else {
			$router[] = new Route('rss.xml', 'Front:Blog:rss');
			$router[] = new Route('user/<presenter>/<action>[/<id>]', array(
				'module' => 'User',
				'presenter' => 'Board',
				'action' => 'default',
			));
			$router[] = new Route('<presenter>/<action>[/<id>]', array(
				'module' => 'Front',
				'presenter' => 'Homepage',
				'action' => 'default',
			));
		}
		return $router;
	}

}
```

Toto je reálná funkční ukázka (ze které jsem něco nepodstatného umazal). Jak je vidět, tak aplikaci mám rozdělenou na moduly, takže defaultní routa ukazuje do modulu Front, pak je k dispozici User modul, link na RSS a konečně CliRouter, který se naroutuje pouze v případě, že běží aplikace v konzolovém módu (CLI).

Pokud se teď přesunu k presenterové části modulu Cli, mohu zde umístit dvě třídy. Klasický BasePresenter, který bude pro jistotu kontrolovat, jestli se opravdu jedná o consoleMode například takto:

```php
<?php

namespace App\CliModule;

use Nette;

abstract class BasePresenter extends Nette\Application\UI\Presenter {

	public function startup() {
		parent::startup();
		if (!$this->context->parameters['consoleMode']) {
			throw new Nette\Security\AuthenticationException;
		}
	}

}
```

No a pak už stačí jen CliPresenter, který bude dědit od BasePresenteru, takže vždy dojde ke kontrole. Zde stačí metoda action*(), která se spustí podle naroutování. V mém případě se tedy jedná o actionCron():

```php
<?php

namespace App\CliModule;

use Nette;

class CliPresenter extends BasePresenter {

	public function actionCron() {
		echo 'FUNGUJU!';
		$this->terminate();
	}

}
```

A teď to nejdůležitější! Aplikace se spustí pomocí terminálu jednoduchým příkazem <code>php index.php</code>. Samozřejmě je nutné ukázat na index Nette aplikace. No a samozřejmě se mohu odkázat i na jinou část aplikace dopsání parametru. Pokud bych chtěl podle výše uvedených souborů přejít na hlavní stránku, stačí napsat pouze <code>php index.php Front:Homepage:default</code>.
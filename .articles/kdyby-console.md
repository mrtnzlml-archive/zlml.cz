Existují knihovny, bez kterých bych si vývoj webových aplikací již téměř nedokázal představit. Jedním z nich je [Kdyby\Console](https://github.com/kdyby/console). Již dříve jsem sice napsal článek o [Nette CliRouteru](nette-2-1-dev-clirouter), ale postupem času a hlavně také díky Doctrine jsem velmi rychle a rád přešel na jiné a dokonalejší řešení. Vzhledem k tomu, že na jednom projektu používám tuto knihovnu velmi hodně a vlastně na ní celý projekt stojí, rád bych alespoň prostřednictvím tohoto článku autorovi poděkoval ([Filip Procházka](https://github.com/fprochazka)). Zároveň bych tímto počínáním rád postupně smazával věčný problém těchto knihoven, protože většinou z hlediska návštěvníka ani není jasné, na co ta knihovna je...

# Proč uvažovat o konzoli?

Pro mě je tato otázka celkem jednoduchá, protože mám projekty, které nejedou jen na sdíleném hostingu, ale jsou to samostatně stojící aplikace. Z toho plyne, že je často zapotřebí vykonávat pomocí CRONu velké množství úkolů. Toto je hlavní část, proč vůbec o konzolovém nástroji uvažuju. Použití je totiž velmi jednoduché a právě samotná Doctrine nabízí prostřednictvím Kdyby\Console celou řadu klasických příkazů a je škoda je nevyužívat. Stačí spustit z příkazové řádky `php index.php` u aplikace a pokud jsou příkazy zaregistrovány v konfigu, vypíše se jejich seznam včetně nápověd:

```
C:\xampp\htdocs\zeminem.cz\www>php index.php
Nette Framework version 2.2.3-RC2

Usage:
  [options] command [arguments]

Options:
  --help           -h Display this help message.
  --quiet          -q Do not output any message.
  --verbose        -v|vv|vvv Increase the verbosity of messages: 1 for normal output, 2 for more verbose output and 3 for debug
  --version        -V Display this application version.
  --ansi              Force ANSI output.
  --no-ansi           Disable ANSI output.
  --no-interaction -n Do not ask any interactive question.

Available commands:
  help                       Displays help for a command
  list                       Lists commands
blog
  blog:install               Install database schema (set-up DB credentials in config.local.neon).
  blog:update                Update database schema (set-up DB credentials in config.local.neon).
dbal
  dbal:import                Import SQL file(s) directly to Database.
orm
  orm:clear-cache:metadata   Clear all metadata cache of the various cache drivers.
  orm:clear-cache:query      Clear all query cache of the various cache drivers.
  orm:clear-cache:result     Clear all result cache of the various cache drivers.
  orm:convert-mapping        Convert mapping information between supported formats.
  orm:convert:mapping        Convert mapping information between supported formats.
  orm:generate-entities      Generate entity classes and method stubs from your mapping information.
  orm:generate-proxies       Generates proxy classes for entity classes.
  orm:generate:entities      Generate entity classes and method stubs from your mapping information.
  orm:generate:proxies       Generates proxy classes for entity classes.
  orm:info                   Show basic information about all mapped entities
  orm:schema-tool:create     Processes the schema and either create it directly on EntityManager Storage Connection or generate the SQL output.
  orm:schema-tool:drop       Drop the complete database schema of EntityManager Storage Connection or generate the corresponding SQL output.
  orm:schema-tool:update     Executes (or dumps) the SQL needed to update the database schema to match the current mapping metadata.
  orm:validate-schema        Validate the mapping files.
```
Díky této možnosti je možné rychle validovat Doctrine entity, nebo generovat SQL schéma přímo do databáze. Použití je opět jednoduché, např.: `php index.php orm:info`.

# Tvorba vlastního příkazu

Prvně je třeba si uvědomit, že tato knihovna je vlastně to samé jako je v [Symfony Console Component](http://symfony.com/doc/current/components/console/introduction.html), tzn. že potřebné informace se dají čerpat právě z této dokumentace a navíc existuje celá sada helperů, jako je například [Progress Bar](http://symfony.com/doc/current/components/console/helpers/progressbar.html), nebo třeba šikovný [Table](http://symfony.com/doc/current/components/console/helpers/table.html). Samotné napsání třídy (Commandu) je pak triviální záležitostí:

```php
<?php

namespace App\Console;

use Doctrine;
use Entity;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class BlogInstall extends Command {

	/** @var \Kdyby\Doctrine\EntityManager @inject */
	public $em;

	protected function configure() {
		$this->setName('blog:install')->setDescription('Install database schema (set-up DB credentials in config.local.neon).');
	}

	protected function execute(InputInterface $input, OutputInterface $output) {
		try {
        	// Zde vykonáme vše co je potřeba
			// Zde vykonáme vše co je potřeba
            // Zde vykonáme vše co je potřeba
            //...
			$output->writeLn('<info>[OK] - BLOG:INSTALL</info>');
			return 0; // zero return code means everything is ok
		} catch (\Exception $exc) {
			$output->writeLn('<error>BLOG:INSTALL - ' . $exc->getMessage() . '</error>');
			return 1; // non-zero return code means error
		}
	}

}
```

Za povšimnutí stojí fakt, že tyto třídy jsou vedle presenterů dalším kandidátem na použití `@inject` anotace. V tomto příbadě bude tedy k dispozici příkaz `blog:install`, který je však nutné ještě zaregistrovat v konfiguračním souboru:

```neon
services:
	-
		class: App\Console\BlogInstall
		tags: [kdyby.console.command]
```

Tento způsob registrace je jedna z věcí, která mě štve a rád bych, kdyby se toto Console naučila hackovat sama a já nemusel tagovat, že se jedná o command. Když je totiž těchto příkazů hodně, konfigurační soubor tímto způsobem poměrně rychle roste na své délce a stává se nepřehledným... (-:

# A co je na tom?

Vždyť toto umí Symfony. To Kdyby nic jiného neumí? No, tak krom toho, že vůbec řeší integraci do Nette, což je asi hlavní úkol, tak jsou logicky součásti integrace i další části jako jsou například vlastní helpery. Není totiž nic horšího, než když v takovém commandu potřebujete presenter. Ale ono je to vlastně jednoduché:

```php
$presenter = $this->getHelper('presenter')->getPresenter();
```

A stejný problém je pak s odkazy. Jak totiž v CLI pracovat s URL, když žádná není? I to Console řeší. Stačí v configu uvést:

```neon
console:
	url: http://zlml.cz/
```

Pak je tvorba odkazů v CLI úplná pohodička:

```php
$link = $presenter->link('//:Front:Homepage:default');
```

Podívejte se na [reálné ukázky](https://github.com/mrtnzlml/zlml.cz/tree/6d1ad3de5b1f98067a38d2085e4939cd17cf5db5/app/commands) příkazů pro Kdyby\Console. Za sebe mohu tento nástroj jedině doporučit. Pokud potřebujete se svojí aplikací pracovat z příkazové řádky. Toto je jiná správná cesta. Díky! (-:
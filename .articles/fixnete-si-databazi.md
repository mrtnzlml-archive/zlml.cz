Možná to znáte. Již nějaký čas nepoužíváte žádný SQL soubor a strukturu databáze si generujete z entit pomocí Doctrine. Je to super, rychlé a funguje to. Paráda. Jenže málokterá databáze se obejde bez nějakých inicializačních dat. Jenže jak na to?

# První přístup

Nebudu ho popisovat moc dlouho, protože ukazuje přesně to, co nechci ukázat. Jendoduše si napíšete nějaké to SQL, které pak nahrnete do databáze. Třeba nějak takto:

```sql
REPLACE INTO `options` (`key`, `value`)
VALUES
('option1', 'value1'),
('option2', 'value2'),
('option3', 'value3');
```

To jak si to pošlete do databáze je celkem jedno. Jestli ručně, nebo přes PHP. Pořád někde zůstává SQL. Proč mi to vadí? Tak třeba zde na blogu je nějaká instalace. A protože jsem se ještě nedokopal k tomu to přepsat, tak musím mít tyto soubory dva. Jeden pro MySQL a druhý pro PosgreSQL. //(Jo správně, blog jde nainstalovat na více databází...)// A to je voser.

Ale jsou i projekty, kde jsem to udělal rovnou pořádně (i když jsou jen na jedné databázi).

# Lepší přístup pomocí fixtures

Znáte [Doctrine Data Fixtures Extension](https://github.com/doctrine/data-fixtures)? Neznáte? Tak to doporučuji, protože vám pomohou vyřešit přesně tento problém. Lépe tuto knihovnu poznáte pomocí composeru:

```
composer require doctrine/data-fixtures
```

Samozřejmě je takový nepsaný předpoklad, že používáte Doctrine... :) Co dál? Ještě než se pustím do dalšího vysvětlování, bylo by fajn napsat si nějaký command. Na takový command objekt se nejlépe hodí moje oblíbená knihovna [Kdyby/Console](https://github.com/Kdyby/Console), která integruje [command ze Symfony](http://symfony.com/doc/current/components/console/introduction.html). Už jsem o tom psal něco málo [dříve](kdyby-console). A díky této přehršli odkazů již víte jak na to a můžeme rovnou nějaký psát. A protože jsem líný programátor, tak se podívám jak to vyřešil [někdo jiný](https://github.com/doctrine/DoctrineFixturesBundle/blob/master/Command/LoadDataFixturesDoctrineCommand.php). A trošku si to zjedoduším:

```php
<?php

use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Loader;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Kdyby\Doctrine\EntityManager;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class DefaultData extends Command
{

	/** @var EntityManager @inject */
	public $em;

	protected function configure()
	{
		$this
			->setName('orm:demo-data:load')
			->setDescription('Load data fixtures to your database.');
            //->addOption...
    }
    
    protected function execute(InputInterface $input, OutputInterface $output)
	{
		try {
        	$loader = new Loader();
			$loader->loadFromDirectory(__DIR__ . '/../basic');
            $fixtures = $loader->getFixtures();

			$purger = new ORMPurger($this->em);
            
            $executor = new ORMExecutor($this->em, $purger);
			$executor->setLogger(function ($message) use ($output) {
				$output->writeln(sprintf('  <comment>></comment> <info>%s</info>', $message));
			});
			$executor->execute($fixtures);
			return 0; // zero return code means everything is ok
        } catch (\Exception $exc) {
			$output->writeLn("<error>{$exc->getMessage()}</error>");
			return 1; // non-zero return code means error
		}
	}
}
```

Ok, to jsem to možná ořezal více než je třeba. Mrkněte na tu ukázku pro Symfony, bude to velmi podobné. A teď už konečně k samotným fixture objektům. To jsou ty co načítám ze složky basic pomocí `loadFromDirectory`. Jedná o objekty, které implementují interface `FixtureInterface`, nebo možná lépe dědí od abstraktní třídy `AbstractFixture`. Obojí je v `Doctrine\Common\DataFixtures` namespace. Objekt obsahující defaultní uživatele může vypadat takto:

```php
<?php

use Doctrine\Common\Persistence\ObjectManager;
use Nette\Security\Passwords;

class UsersFixture extends \Doctrine\Common\DataFixtures\AbstractFixture
{

	public function load(ObjectManager $manager)
	{
		$admin = new \Users\User('admin@nette.org');
		$admin->setPassword(Passwords::hash('admin'));
		$admin->addRole($this->getReference('admin-role'));
		$manager->persist($admin);

		$demo = new \Users\User('demo@nette.org');
		$demo->setPassword(Passwords::hash('demo'));
		$demo->addRole($this->getReference('demo-role'));
		$manager->persist($demo);

		$manager->flush();

		$this->addReference('admin-user', $admin);
		$this->addReference('demo-user', $demo);
	}

}
```

V čem je to tak parádní? Používám PHP kód, používám vlastní nadefinované entity. Hned vidím, že mi to fugnuje, ověřuji svůj návrh databáze a rovnou poskytuji dalším ukázku toho, jak jsem to myslel. Za povšimnutí stojí funkce `addReference` a `getReference`. Je jasné, že v každé relační databázi budou nějaké relace a právě k tomu tyto funkce slouží. Vytvořím si tedy nějaké ukazatele a ty pak mohu použít v jiné části demo dat. Lépe to  bude vidět na druhé tabulce:

```php
<?php

use Doctrine\Common\Persistence\ObjectManager;

class RolesFixture extends \Doctrine\Common\DataFixtures\AbstractFixture
{

	public function load(ObjectManager $manager)
	{
		$user = new \Users\Role();
		$user->setName(\Users\Role::DEMO_USER);
		$manager->persist($user);

		$admin = new \Users\Role();
		$admin->setName(\Users\Role::ADMIN);
		$manager->persist($admin);

		$manager->flush();

		$this->addReference('demo-role', $user);
		$this->addReference('admin-role', $admin);
	}

}
```

Vidíte? Mám role, vytvořím si na ně odkaz a používám je při definici uživatele. Vyzkoušejte si to. Uvidíte, jak se krásně naplní referenční tabulky a vše bude tak, jak to má být...

Jen pozor na jedno věc. Ohlídejte si [pořadí těchto objektů](https://github.com/doctrine/data-fixtures#fixture-ordering). To lze vyřešit implementací rozhraní `OrderedFixtureInterface`, nebo `DependentFixtureInterface`, což je o něco lepší přístup.

A jak toto celé použít? Však už to znáte:

```
λ php index.php
λ php index.php orm:schema-tool:create
λ php index.php orm:demo-data:load
```

První příkaz vám nabídne všechny dostupné příkazy, druhý vygeneruje strukturu databáze bez dat a poslední spustí natažení demo dat. Pak už se jen kocháte:

```
λ php index.php orm:demo-data:load --demo
Careful, database will be purged. Do you want to continue Y/N ? y
  > purging database
  > loading RolesFixture
  > loading UsersFixture
  > loading ArticlesFixture
  > loading ProductsFixture
  ...
```
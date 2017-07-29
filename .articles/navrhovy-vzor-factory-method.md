Návrhový vzor Factory Method má za úkol definovat rozhraní pro vytváření objektů s tím, že vlastní tvorbu instancí přenechává potomkům. Samotný návrhový vzor tedy tvoří například tyto dvě třídy.

```php
<?php
abstract class ACreator {
	protected $attribute;
	public function __construct($attribute) {
		$this->attribute = $attribute;
	}
	abstract public function createConcreteObject();
}
```

```php
<?php
class ConcreteCreator extends ACreator {
	public function createConcreteObject() {
		$concreteObject = new ConcreteObject($this->attribute);
		return $concreteObject;
	}
}
```

Aby však tento vzor měl nějaký smysl, je potřeba ještě minimálně jedna třída od které se budou dělat instance (ConcreteObject).

```php
<?php
class ConcreteObject implements IObject {
	protected $attribute;
	public function __construct($attribute) {
		$this->attribute = $attribute;
	}
	public function test() {
		echo $this->attribute;
	}
}
```

Případně jeho rozhraní:

```php
<?php
interface IObject {
	public function test();
}
```

Vraťme se však na začátek. Vytvářet objekty všichni umí. Slouží k tomu známý operátor <code>new</code>. Na tom není nic divného, ale jen do chvíle, než se nad tím zamyslíte. Představte si rozsáhlou aplikaci, kde na každém rohu potřebujete vytvořit instanci určitého objektu. Takže jako vždy zavoláte operátor <code>new</code> a předáte všechny potřebné argumenty. A pak se to stane. Najednou zjistíte, že nutně potřebujete přidat do konstruktoru argument/y a máte týden co dělat. K tomu se právě hodí vytvořit si továrnu na tyto instance, kdy budeme pouze volat metodu pro její vytvoření, ale to jak se vytvoří necháme na továrně. Obecně se ve světě OOP velmi často dělá, že nějakou práci prostě necháme na někom jiném. Je to funkční a pohodlný přístup. :-)

Pokud tedy zapomenu na to, že existují nějaké třídy Creator, tak by použití třídy ConcreteObject vypadalo asi takto:

```php
$instance = new ConcreteObject('TEST');
$instance->test();
```

Při zapojení továrny je použití zdánlivě složitější.

```php
$factory = new ConcreteCreator('TEST');
$instance = $factory->createConcreteObject();
$instance->test();
```

Přidaná hodnota tohoto postupu je však velká. Již nejsme vázání na konkrétní implementaci objektu ConcreteObject. Vlastně nás to vůbec nezajímá. Víme, že pro jeho tvorbu potřebuje továrna nějaký atribut a to, jestli pak ještě další tři přidá, nebo ne, nám může být úplně jedno. Kdo stále ještě nevěří, že je tento postup výhodný, nechť si vyzkouší vytvořit několik instancí stejného objektu (hloupý, ale názorný příklad).

```php
// pomocí operátoru new:
$instance = new ConcreteObject('TEST');
$instance = new ConcreteObject('TEST');
$instance = new ConcreteObject('TEST');
$instance = new ConcreteObject('TEST');
$instance = new ConcreteObject('TEST');

// pomocí továrny:
$factory = new ConcreteCreator('TEST');
$instance = $factory->createConcreteObject();
$instance = $factory->createConcreteObject();
$instance = $factory->createConcreteObject();
$instance = $factory->createConcreteObject();
$instance = $factory->createConcreteObject();
```

A teď přidejme nový atribut - aktuální rok.

```php
// pomocí operátoru new:
$instance = new ConcreteObject('TEST', date('Y'));
$instance = new ConcreteObject('TEST', date('Y'));
$instance = new ConcreteObject('TEST', date('Y'));
$instance = new ConcreteObject('TEST', date('Y'));
$instance = new ConcreteObject('TEST', date('Y'));

// pomocí továrny:
$factory = new ConcreteCreator('TEST', date('Y'));
$instance = $factory->createConcreteObject();
$instance = $factory->createConcreteObject();
$instance = $factory->createConcreteObject();
$instance = $factory->createConcreteObject();
$instance = $factory->createConcreteObject();
```

Krom toho, že by mě za chvíli přestalo bavit do každého konstruktoru kopírovat nový atribut, tak jsem také mohl udělat o hodně více chyb než u továrny. Pravdou je, že jsem musel upravit ještě pár tříd:

```php
<?php
class ConcreteObject implements IObject {
	protected $attribute;
	protected $year;
	public function __construct($attribute, $year) {
		$this->attribute = $attribute;
		$this->year = $year;
	}
	// ...
}

abstract class ACreator {
	protected $attribute;
	protected $year;
	public function __construct($attribute, $year) {
		$this->attribute = $attribute;
		$this->year = $year;
	}
	abstract public function createConcreteObject();
}

class ConcreteCreator extends ACreator {
	public function createConcreteObject() {
		$concreteObject = new ConcreteObject($this->attribute, $this->year);
		return $concreteObject;
	}
}
```

Nicméně křivka výhod při používání továrny velmi rychle překoná svým stoupáním křivku lenosti při používání operátoru new.

Mimochodem vzpomeňte si na [osm návrhových přikázání](osm-navrhovych-prikazani), kde se také říká, že máme vždy programovat vůči rozhraní, a nikdy ne vůči konkrétní implementaci, což tento návrhový vzor perfektně splňuje.
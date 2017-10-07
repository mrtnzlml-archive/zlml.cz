---
id: e9cd5692-13b4-495f-a63d-1dd5f4923fcb
timestamp: 1356550681000
title: Návrhový vzor Singleton
slug: navrhovy-vzor-singleton
---
Návrhový vzor Singleton je velmi známý. Má za úkol zajistit, že bude z určité třídy existovat pouze jedna instance. K této instanci poskytne globální přístupový bod. Jednoduché zadání, ale samotná implementace může být v PHP zákeřná. Proč? Tak prvně záleží na tom, jak budeme u návrhu striktní.

```php
<?php
class Object {
	private static $instance = null;
	public static function getInstance() {
		if(self::$instance === null) {
			self::$instance = new self;
		}
		return self::$instance;
	}
}
```

Takovouto třídu lze totiž rovnou prohlásit za návrhový vzor Singleton. Dává to smysl, protože můžeme zavolat:

```php
$obj1 = Object::getInstance();
$obj2 = Object::getInstance();
```

Tím se pokusíme vytvořit dvě instance této třídy, ve skutečnosti se však vytvoří jen jedna. Tyto objekty jsou identické, což lze dokázat jednoduchou zkouškou:

```php
if($obj1 === $obj2) {
	echo '$obj1 === $obj2'; //TRUE
} else {
	echo '$obj1 !== $obj2';
}
```

Singleton to je a nikdo nemůže říct ne. Jak jsem však již psal, záleží na tom, jak budeme u návrhu striktní, protože by to nebylo PHP, kdyby neexistovalo několik otazníků a háčků. Pravděpodobně spoustu lidí totiž napadne, že metoda <code>getInstance()</code> je sice hezká, ale vůbec ji nemusím použít. V takovém případě celý princip Singletona padá.

```php
$obj1 = Object::getInstance();
$obj2 = new Object();

if($obj1 === $obj2) {
	echo '$obj1 === $obj2';
} else {
	echo '$obj1 !== $obj2'; //TRUE
}
```

To je jasné, zatím ve třídě neexistuje žádný mechanismus, který by zakázal používání konstruktoru. K tomu je potřeba pouze malá úprava třídy.

```php
<?php
class Object {
	private static $instance = null;
	public static function getInstance() {
		if(self::$instance === null) {
			self::$instance = new self;
		}
		return self::$instance;
	}
	protected function __construct() {}
}
```

V tento moment již nepůjde objekt vytvořit pomocí operátoru <code>new</code>. Případný pokus skončí fatální chybou. Nebylo by to však PHP, kdyby neexistovalo několik dalších otazníků a háčků. S ledovým klidem si totiž mohu první vytvořený objekt naklonovat a tím opět získám dvě nezávislé instance jedné třídy. Ale to jsem přece nechtěl!

```php
$obj1 = Object::getInstance();
$obj2 = clone $obj1;

if($obj1 === $obj2) {
	echo '$obj1 === $obj2';
} else {
	echo '$obj1 !== $obj2'; //TRUE
}
```

Proti tomuto postupu se mohu bránit opět podobným způsobem jako u konstruktoru.

```php
<?php
class Object {
	private static $instance = null;
	public static function getInstance() {
		if(self::$instance === null) {
			self::$instance = new self;
		}
		return self::$instance;
	}
	protected function __construct() {}
	private function __clone() {}
}
```

Pokus o naklonování již vytvořené instance pomocí metody <code>getInstance()</code> skončí opět fatální chybou. Jenže nebylo by to PHP, kdyby... Co se stane, když vytoření objekt serializuji a pak ho zase deserializuji?

```php
$obj1 = Object::getInstance();
$obj2 = unserialize(serialize($obj1));

if($obj1 === $obj2) {
	echo '$obj1 === $obj2';
} else {
	echo '$obj1 !== $obj2'; //TRUE
}
```

To už začíná být poněkud otravné. Ale tak dobře, i to se dá ošéfovat.

```php
<?php
class Object {
	private static $instance = null;
	public static function getInstance() {
		if(self::$instance === null) {
			self::$instance = new self;
		}
		return self::$instance;
	}
	protected function __construct() {}
	private function __clone() {}
	private function __wakeup() {}
}
```

Toto už sice vrátí jen warning, ale víme o tom. Schválně jsem nepsal, že se to vše dá ošetřit, protože je to spíše zákaz (popř. upozornění). Je samozřejmě možné (lepší) vracet různé vyjímky atd. Stejně tak je spousta variant jak psát přítupové modifikátory k metodám. To jednoduše vše zaleží na tom, co od Singleton objektu očekáváme a kdo objekt používá, protože jak jsem již psal, úplně klidně stačí Singleton s jednou metodou <code>getInstance()</code>. Svoji funkci plní, tečka.

```php
<?php
class Object {
	private static $instance = null;
	public static function getInstance() {
		if(self::$instance === null) {
			self::$instance = new self;
		}
		return self::$instance;
	}
	protected function __construct() {}
	public final function __clone() {
		throw new Exception('Objekt nelze klonovat!');
	}
	public final function __wakeup() {
		throw new Exception('Objekt nelze deserializovat!');
	}
}
```
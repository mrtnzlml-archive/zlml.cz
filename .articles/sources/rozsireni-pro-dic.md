---
timestamp: 1457796154000
title: Rozšíření pro DIC
slug: rozsireni-pro-dic
---
Jednu z věcí, které jsem zde na blogu moc nepopsal jsou rozšíření pro DIC (Dependency Injection Container, potomek `Nette\DI\Container`). A protože se chci vrátit jednodušším článkům, zaměřím se na úplně základy. Jaká je motivace k psaní rozšíření DIC a co to vlastně je?

Své aplikace rozděluji poměrně důsledně na jakési balíčky (bundles - název ze Symfony). Cílem je rozškatulkovat celou aplikaci podle logických celků, tzn. každý bundle by se měl starat pouze o tu svojí věc a zároveň si s sebou nést vše potřebné. Jedná se o balíčky typu *Articles*, který se stará (jak sám název napovídá) pouze a jenom o články, nebo *Eshop*, *GoPay*, *Media*, *Users*, atd. Výhodou tohoto přístupu je to, že mohu kdykoliv z balíčku udělat Composer balíček a úplně ho oddělit od aplikace. Zároveň některé balíčky směřují k jednoduchému oddělení do [microservices](http://martinfowler.com/articles/microservices.html). Nevýhodou je pak to, že je to oproti běžným způsobům jak navrhovat aplikaci poměrně složité.

Napsat takto aplikaci většinou zamená zasahovat do nějaký globálních prostor, popřípadě mít vytvořené nějaké body v aplikaci, na které je možné se navěsit. Jelikož však využívám(e) Nette, je nejlepší způsob využít právě rozšíření DIC.

# Píšeme první rozšíření DIC

Celé je to vlastně velmi jednoduché. Třeba do `src/Articles/DI` umístíme třídu `ArticlesExtension`, která dědí od `Nette\DI\CompilerExtension`. Struktura je libovolná, podstatné je, aby třída dědila právě od *CompilerExtension*. Druhá věc, kterou je třeba udělat, je zaregistrování našeho nového rozšíření do DI kontejneru. To uděláme velice jednoduše v konfiguračním souboru:

```neon
extensions:
	- Ant\Articles\DI\ArticlesExtension
```

V tuto chvíli je již rozšíření funkční a Nette s ním počítá. Jen zatím nic neumí. To napravíme velmi rychle, nejdříve však trošku nezbytné teorie. Takto zaregistrované rozšíření se zpracovává pouze v okamžiku kompilace DIC. Na toto je důležité myslet - je to jedna z nejčastějších chyb začátečníků. Ono to dává smysl. Je to rozšíření DIC. Kontejner se tedy jednou nějak pomocí rozšíření upraví, vygeneruje, uloží a hotovo.

Druhá důležitá informace se týká životního cyklu rozšíření. Při kompilaci (generování) DIC se každé zaregistrované rozšíření volá celkem třikrát a pokaždé se spustí jiná metoda. Je to podobné jako [životní cyklus presenteru](https://doc.nette.org/cs/2.3/presenters#toc-zivotni-cyklus-presenteru).

První volanou metodou je `loadConfiguration` a volá se v okamžiku, kdy Nette začne s rozšířením pracovat. V tu chvíli je již k dispozici konfigurace jednoho konkrétního rozšíření. Kde se ale tato konfigurace vezme? Tak to se musíme vráti kousek zpět a rozšíření zaregistrovat trošku jinak:

```neon
extensions:
	articles: Ant\Articles\DI\ArticlesExtension
```

Díky tomu, že je rozšíření pojmenované a ne anonymní jako to bylo doposud, můžeme rozšíření předat libovolnou vlastní konfiguraci:

```neon
articles:
	option_1: value_1
	option_2: value_2
	# ...
```

A přesně tyto hodnoty (`option_1`, `option_2`) můžeme získat jako pole pomocí metody `$this->getConfig()` v rozšíření. K čemu se tedy `loadConfiguration` hodí? Jedná se o místo, kde je vhodné načíst (a zvalidovat) konfiguraci. Já osobně nejraději načítám extra config, který si s sebou nese samo rozšíření, takže se mi rozšíření zjednoduší na:

```php
public function loadConfiguration()
{
	$this->addConfig(__DIR__ . '/services.neon');
}
```

Kde `services.neon` obsahuje třeba:

```neon
services:
	- Ant\Articles\Components\IArticlesGridFactory
	# ...
```

Je to jednudché a chápe to každý kdo chápe konfigurace. Jen pozor na to, že funkce `addConfig` jsem si napsal sám. Mrkněte se do [dokumentace](https://doc.nette.org/cs/2.3/di-extensions#toc-loadconfiguration) jak se to dá udělat. Pokud se někomu nelíbí mít u každého rozšíření vlastní config, je možné vše napsat v rozšíření ručně. Zde také odkážu na dokumentaci.

Když už je konfigurace všeho připravena, přichází ke slovu druhá funkce `beforeCompile`. Ta se volá v okamžiku, kdy už je skoro vše připraveno, ale ještě se kontejner negeneruje. Já osobně tento čas využívám například k registraci presenter mappingu pro jednotlivé bundly:

```php
public function beforeCompile()
{
	$builder = $this->getContainerBuilder();
    $builder->getDefinition($builder->getByType(IPresenterFactory::class))->addSetup(
        'setMapping',
        [['Articles' => 'Ant\Articles\*Module\Presenters\*Presenter']]
    );
}
```

Tím jsem chtěl ukázat, že se jedná o místo, kde je vhodné upravovat již připravené služby a různě je ještě donastavit s tím, že až se zavolají metody `beforeCompile` nad každým rozšířením, přijde ke slovu poslední metoda a tou je `afterCompile`. Tato metoda dostává v parametrem `Nette\PhpGenerator\ClassType` což je jinak řečeno vygenerovaný DI kontejner v paměti, tedy ještě neuložený do souboru (cache). Existuje tedy ještě poslední možnost jak s DIC ještě něco udělat. Stačí využít síly `Nette\PhpGenerator` a můžete tvořit kouzla. Většinou se však poupravuje metoda DIC `initialize` třeba jako to dělá `Nette\DI\Extensions\ConstantsExtension` nebo [PhpExtension](https://api.nette.org/2.3.9/source-DI.Extensions.PhpExtension.php.html#19-48):

```php
public function afterCompile(Nette\PhpGenerator\ClassType $class)
{
	foreach ($this->getConfig() as $name => $value) {
		$class->getMethod('initialize')->addBody('define(?, ?);', array($name, $value));
	}
}
```

*Initialize* je totiž metoda, která se volá jako jedna z [prvních vůbec](https://api.nette.org/2.3.9/source-Bootstrap.Configurator.php.html#224).

Tolik jinak (pro normální lidi) napsáno to, co je v [dokumentaci](https://doc.nette.org/cs/2.3/di-extensions). Mělo by teď být jasné, k čemu je psaní rozšíření pro DIC vůbec dobré a proč by o tom měl člověk vůbec uvažovat. Je zřejmé, že je to zase další složitost navíc, ale když programátor ovládne psaní takových rozšíření, celá aplikace se najednou začne pěkně škatulkovat a rozpadat na jednodušší části. Ostatně takto se píšou všechny normální [addony](https://componette.com/) pro Nette.

# Ještě nějaké jednoduché testy

Špatný test je sice pořád špatný, zároveň je však lepší, než test žádný. Všímám si však toho, že se o testech hodně a dlouho blábolí, ale když přijde ten správný čas, tak o nich nepadne ani slovo. Proto jsem je začal ve svých ukázkách hodně tlačit a blog nezůstane pozadu. Jak by tedy takový jednoduchý test mohl vypadat? Asi nebude překvapením, když na to použiju svůj [mrtnzlml/testbench](https://github.com/mrtnzlml/testbench), který si může kdokoliv stáhnout a který mi ušetří hodně trápení. K testu mi bude stačit pouze traita `Testbench\TCompiledContainer` a celý test case by mohl vypadat třeba takto:

```php
<?php

namespace Ant\Tests\Articles;

use Ant\Articles\Components\IArticlesGridFactory;
use Tester\Assert;

require __DIR__ . '/../bootstrap.php';

/**
 * @testCase
 */
class Extension extends \Tester\TestCase
{

	use \Testbench\TCompiledContainer;

	public function testFunctionality()
	{
		$articlesGridFactory = $this->getService(IArticlesGridFactory::class);
		Assert::type('Ant\Articles\Components\IArticlesGridFactory', $articlesGridFactory);
		Assert::type('Ant\Articles\Components\ArticlesGrid', $articlesGridFactory->create(NULL));
	}

}

(new Extension)->run();
```

Co to vlastně testuje? Podívejte se na obsah `service.neon`, kde jsem dříve v rozšíření přidával najkou generovanou továrničku `IArticlesGridFactory`. V první řadě si tak otestuji, že je zaregistrována správně a že ji kontejner zná. Když mám továrničku, tak mohu ještě otestovat, jestli je vůbec možné vytvořit komponentu pro kterou je tato továrna určena. To už je skoro nadbytečný test, protože to bych si měl asi otestovat až v testu pro komponentu, ale tak proč ne.

To nebylo tak složité, že? A navíc je to celé pokryté testy. Nádhera... :)
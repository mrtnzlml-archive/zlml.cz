---
id: 45b197e5-9e04-43d5-b48e-58f9a7f90887
timestamp: 1478454487000
title: "Tester: vlastní Output Handler"
slug: tester-vlastni-output-handler
---
Output Handler umožňuje změnit finální podobu výstupu z Nette Testeru. Výstup může [vypadat například takto](https://travis-ci.org/adeira/connector/jobs/173698139). Osobně se mi tento výstup líbí víc, protože místo teček rovnou vidím co se skutečně spouští. Může se to hodit a sám jsem se přistil, že občas spouštím testy takto:

```
vendor/bin/run-tests -o tap
```

A to jen proto, abych viděl co se zrovna testuje (TAP). Napsat si vlastní výstupní handler je jednoduché. Jen je třeba dávat pozor na to, co je [napsáno v dokumentaci](https://tester.nette.org/#toc-setup-path), protože to [nemusí být dobře](https://github.com/nette/web-content/pull/473)... :)

Stačí Tester spouštět s přepínačem `--setup`:

```
vendor/bin/tester --setup tests/runner-setup.php

# or Testbench edition:
vendor/bin/run-tests --setup tests/runner-setup.php
```

Skript `runner-setup.php` potom obsahuje samotný handler který může vypadat třeba takto (PHP 7):

```php
<?php declare(strict_types = 1);

use Tester\Dumper;
use Tester\Runner\Runner;

/** @var \Tester\Runner\Runner $runner */
$runner->outputHandlers = []; // delete native output handlers
$runner->outputHandlers[] = new class ($runner) extends \Tester\Runner\Output\ConsolePrinter
{

	public function begin()
	{
		ob_start();
		parent::begin();
		echo rtrim(ob_get_clean()) . ' | ' . getenv('BOOTSTRAP') . "

";
	}

	public function result($testName, $result, $message)
	{
		$outputs = [
			Runner::PASSED => Dumper::color('green', '✔ ' . $testName),
			Runner::SKIPPED => Dumper::color('olive', 's ' . $testName) . "($message)",
			Runner::FAILED => Dumper::color('red', '✖ ' . $testName) . "
" . $this->indent($message, 3) . "
",
		];
		echo $this->indent($outputs[$result], 2) . PHP_EOL;
	}

	public function end()
	{
		ob_start();
		parent::end();
		echo "
" . trim(ob_get_clean()) . "
";
	}

	private function indent($message, $spaces)
	{
		if ($message) {
			$result = '';
			foreach (explode(PHP_EOL, $message) as $line) {
				$result .= str_repeat(' ', $spaces) . $line . PHP_EOL;
			}
			return rtrim($result, PHP_EOL);
		}
		return $message;
	}

};
```

Je to vlastně jen o třech metodách. Začátek `begin` a konec `end` slouží jen k ořezání mezer popř. k doplnění dodatečných informací. Nejzajímavější je metoda `result`, která velmi mění způsob vykreslení jednotlivých řádek. Bohužel Tester sám od sebe ořezává výstupní texty a podle toho jak jsem to rychle prohlížel, tak s tím nejde nic moc udělat. Představoval bych si, že výstupní texty budou trošku lepší, ale to bez PR do `Nette\Testr`u asi nepůjde...

To by bylo. Pozornější čtenáři kódu mohou mít teď otázku co je to `getenv('BOOTSTRAP')`? Dlouze jsem řešil jak psát testy s ohledem na to, že se mi nechce pořád dělat require `bootstrap.php`, protože mám testy hodně zanořené a cesty k tomutou souboru bývají hodně dlouhé. Navíc je to nesmírně limitující, protože nelze jednoduše bez úpravy testů měnit adresářovou strukturu. Řešením je trošku to obejít:

```php
require getenv('BOOTSTRAP');
```

Existuje [více řešení](https://github.com/nette/tester/issues/275), ale toto mi sedělo nejlépe. Užitečné je pak napsat si vlastní skript `tests/run` a všechno to spojit:

```bash
#!/usr/bin/env bash

BOOTSTRAP=$(pwd)/tests/bootstrap.php vendor/bin/run-tests --setup tests/runner-setup.php
```

Spuštění je tak jednoduché, jako je jednoduché napsat `tests/run`. Pokud by si to chtěl někdo prohlédnout více detailně a popř. si s tím pohrát, tak je vše zde popisované implementováno v projektu [Adeira\Connector na GitHubu](https://github.com/adeira/connector).

:)
<?php declare(strict_types = 1);

namespace App\Routing\DI;

class Extension extends \Nette\DI\CompilerExtension
{

	public function provideConfig()
	{
		return __DIR__ . '/config.neon';
	}

}

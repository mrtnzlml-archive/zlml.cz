<?php declare(strict_types = 1);

namespace App\Social\DI;

class Extension extends \Nette\DI\CompilerExtension
{

	public $defaults = [
		'twitter' => [
			'consumerKey' => NULL,
			'consumerSecret' => NULL,
			'accessToken' => NULL,
			'accessTokenSecret' => NULL,
		],
	];

	public function provideConfig()
	{
		return __DIR__ . '/config.neon';
	}

	public function loadConfiguration()
	{
		$this->validateConfig($this->defaults);
	}

}

<?php declare(strict_types=1);

class CustomTestCase extends Tester\TestCase
{

	use Test\PresenterTester {
		Test\PresenterTester::createContainer as parentCreateContainer;
	}

	protected function createContainer()
	{
		return $this->parentCreateContainer([
			__DIR__ . '/../app/config/config.neon',
			__DIR__ . '/../app/config/config.local.neon',
		]);
	}

}

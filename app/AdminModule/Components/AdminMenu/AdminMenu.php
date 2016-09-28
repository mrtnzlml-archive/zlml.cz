<?php declare(strict_types = 1);

namespace App\AdminModule\Components\AdminMenu;

use Nette\Application\UI;

class AdminMenu extends UI\Control
{

	private $contributors = [];

	public function __construct()
	{
		parent::__construct();
	}

	public function render()
	{
		$this->template->setFile(__DIR__ . '/AdminMenu.latte');
		$this->template->contributors = $this->contributors;
		$this->template->render();
	}

	public function addContributor(MenuItem $entry)
	{
		$this->contributors[] = $entry;
	}

}

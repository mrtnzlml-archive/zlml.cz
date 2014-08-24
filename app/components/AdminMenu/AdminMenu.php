<?php

namespace Cntrl;

use Model;
use Nette\Application\UI;

class AdminMenu extends UI\Control {

	/** @var \Model\Pictures */
	private $pictures;
	/** @var \Model\Tags */
	private $tags;
	/** @var \Model\Users */
	private $users;

	public function __construct(Model\Pictures $pictures, Model\Tags $tags, Model\Users $users) {
		parent::__construct();
		$this->pictures = $pictures;
		$this->tags = $tags;
		$this->users = $users;
	}

	public function render() {
		$this->template->setFile(__DIR__ . '/AdminMenu.latte');
		$this->template->picturecount = $this->pictures->countBy();
		$this->template->tagcount = $this->tags->countBy();
		$this->template->usercount = $this->users->countBy();
		$this->template->render();
	}

}

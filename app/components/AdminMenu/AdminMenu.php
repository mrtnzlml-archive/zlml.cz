<?php

namespace Cntrl;

use App;
use Nette\Application\UI;

class AdminMenu extends UI\Control {

	/** @var \App\Pictures */
	private $pictures;
	/** @var \App\Tags */
	private $tags;
	/** @var \App\Users */
	private $users;

	public function __construct(App\Pictures $pictures, App\Tags $tags, App\Users $users) {
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

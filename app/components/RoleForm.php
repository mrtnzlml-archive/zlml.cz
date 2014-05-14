<?php

namespace Cntrl;

use App;
use Entity;
use Nette\Application\UI;

class RoleForm extends UI\Control {

	public function render() {
		$this->template->setFile(__DIR__ . '/RoleForm.latte');
		$this->template->render();
	}

	protected function createComponentForm() {
		$form = new UI\Form;
		$form->addProtection();

		$form->addSelect('test', 'Někdo může:', array(
			'1' => 'upravovat'
		));

		$form->addSubmit('save', 'Uložit změny');
		$form->onSuccess[] = $this->formSucceeded;
		return $form;
	}

	public function formSucceeded(UI\Form $form) {
		$vals = $form->getValues();
		//TODO
	}

}
<?php

namespace Cntrl;

use App;
use Nette\Application\UI;

class UserEditForm extends UI\Control {

	private $users;
	private $account;

	public function __construct(App\Users $users) {
		parent::__construct();
		$this->users = $users;
	}

	public function render() {
		$this->account = $this->users->findOneBy(['id' => $this->presenter->getParameter('id')]);
		$this->template->setFile(__DIR__ . '/UserEditForm.latte');
		$this->template->render();
	}

	protected function createComponentForm() {
		$form = new UI\Form;
		$form->addProtection();
		$form->addText('username', 'Uživatelské přihlašovací jméno:')
			->setValue($this->account->username)
			->setRequired('Zadejte prosím přihlašovací jméno.');
		$form->addSelect('role', 'Role:', array('admin')); //TODO: samostatná tabulka
		$form->addSelect('perm1', 'Může ...', array('Ano', 'Ne'));
		$form->addSelect('perm2', 'Může ...', array('Ano', 'Ne'));
		$form->addSelect('perm3', 'Může ...', array('Ano', 'Ne'));
		$form->addSubmit('save', 'Uložit změny');
		$form->onSuccess[] = $this->formSucceeded;
		return $form;
	}

	public function formSucceeded(UI\Form $form) {
		$vals = $form->getValues();

		$this->presenter->flashMessage('TODO');
	}

}
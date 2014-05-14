<?php

namespace Cntrl;

use App;
use Entity;
use Nette\Application\UI;

class UserEditForm extends UI\Control {

	public $onSave = [];

	private $users;
	private $account;

	public function __construct(App\Users $users, $id) {
		parent::__construct();
		$this->users = $users;
		$this->account = $this->users->findOneBy(['id' => $id]);
	}

	public function render() {
		$this->template->setFile(__DIR__ . '/UserEditForm.latte');
		$this->template->render();
	}

	protected function createComponentForm() {
		$form = new UI\Form;
		$form->addProtection();
		$form->addText('username', 'Uživatelské přihlašovací jméno:')
			->setValue($this->account->username)
			->setRequired('Zadejte prosím přihlašovací jméno.');
		//TODO: password
		$form->addSelect('role', 'Role:', array(
			'admin' => 'Administrátor'
		))->setDisabled();
		//TODO: nastavení rolí musí být někde samostatně
		$form->addSubmit('save', 'Uložit změny');
		$form->onSuccess[] = $this->formSucceeded;
		return $form;
	}

	public function formSucceeded(UI\Form $form) {
		$vals = $form->getValues();
		try {
			$this->account->username = $vals->username;
			$this->account->role = $vals->role;
			$this->users->save($this->account);
			$this->presenter->flashMessage('Změny úspěšně uloženy.', 'success');
		} catch (\Exception $exc) {
			$this->presenter->flashMessage($exc->getMessage(), 'danger');
		}
		$this->onSave($this, $this->account);
	}

}
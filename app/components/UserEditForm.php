<?php

namespace Cntrl;

use App;
use Entity;
use Nette\Application\UI;
use Nette\Security\Passwords;

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
			->setRequired('Zadejte prosím přihlašovací jméno.');
		$form->addPassword('password', 'Nové heslo k tomuto účtu:')
			->setRequired('Zadejte prosím své stávající, nebo nové heslo.');
		$role = array(
			'admin' => 'Administrátor',
			'demo' => 'Demo účet'
		);
		//FIXME: upravovat práva může jen admin, při zakládání musí být default nějaká menší než admin (ošetřit formSucceeded)
		/*if($this->presenter->user->isInRole('admin') && $this->account->role !== 'admin') {
			$form->addSelect('role', 'Role:', $role);
		} else {
			$form->addSelect('role', 'Role:', $role)->setDisabled();
		}*/
		if ($this->account) {
			$form->setDefaults(array(
				'username' => $this->account->username,
				'role' => $this->account->role,
			));
		}
		$form->addSubmit('save', 'Uložit změny');
		$form->onSuccess[] = $this->formSucceeded;
		return $form;
	}

	public function formSucceeded(UI\Form $form) {
		if (!$this->editable()) {
			$this->presenter->flashMessage('Myslím to vážně, editovat opravdu **ne**můžete!', 'danger');
			$this->redirect('this');
			return;
		}
		$vals = $form->getValues();
		try {
			if (!$this->account) {
				$this->account = new Entity\User();
			}
			$this->account->username = $vals->username;
			$this->account->password = Passwords::hash($vals->password);
			$this->account->role = $vals->role;
			$this->users->save($this->account);
			$this->presenter->flashMessage('Změny úspěšně uloženy.', 'success');
		} catch (\Exception $exc) {
			$this->presenter->flashMessage($exc->getMessage(), 'danger');
		}
		$this->onSave();
	}

	private function editable() {
		return $this->presenter->user->isAllowed('Admin', App\Authorizator::EDIT) ? TRUE : FALSE;
	}

}
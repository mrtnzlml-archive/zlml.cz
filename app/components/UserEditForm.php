<?php

namespace Cntrl;

use Model;
use Entity;
use Kdyby;
use Nette\Application\UI;
use Nette\Security\Passwords;

class UserEditForm extends UI\Control {

	public $onSave = [];
	//public $onBeforeRestrictedFunctionality = [];

	private $users;
	private $account;

	public function __construct(Model\Users $users, $id) {
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
		//TODO: toto bude zapotřebí předělat
		if ($this->presenter->user->isInRole('admin')) {
			$role = [
				'admin' => 'Administrátor',
				'demo' => 'Demo účet'
			];
			$form->addSelect('role', 'Role:', $role);
		} else {
			$role = [
				'demo' => 'Demo účet'
			];
			$form->addSelect('role', 'Role:', $role);
		}
		if ($this->account) {
			$form->setDefaults([
				'username' => $this->account->username,
				//'role' => $this->account->role,
			]);
		} else {
			$form->setDefaults([
				'role' => 'demo',
			]);
		}
		$form->addSubmit('save', 'Uložit změny');
		$form->onSuccess[] = $this->formSucceeded;
		return $form;
	}

	public function formSucceeded(UI\Form $form) {
		//$this->onBeforeRestrictedFunctionality($this); //FIXME: must be registered in config, but it's against generated factories
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
		} catch (Kdyby\Doctrine\DuplicateEntryException $exc) { //DBALException
			$this->presenter->flashMessage('Uživatel s tímto jménem již existuje. Zvolte prosím jiné.', 'danger');
		} catch (\Exception $exc) {
			$this->presenter->flashMessage($exc->getMessage(), 'danger');
		}
		$this->onSave();
	}

	private function editable() {
		return $this->presenter->user->isAllowed('Admin:Admin', Model\Authorizator::CREATE) ? TRUE : FALSE;
	}

}

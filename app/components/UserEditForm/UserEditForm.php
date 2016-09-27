<?php

namespace Cntrl;

use Doctrine;
use Entity;
use Kdyby;
use Model;
use Nette;
use Nette\Application\UI;
use Nette\Security\Passwords;

class UserEditForm extends UI\Control
{

	const DEFAULT_ROLE = 'demo';

	public $onSave = [];
	//public $onBeforeRestrictedFunctionality = [];

	private $roles = [
		'demo' => 'Demo účet',
		'admin' => 'Administrátor',
	];

	private $users;
	private $account;

	public function __construct($id, Model\Users $users)
	{
		parent::__construct();
		$this->users = $users;
		$this->account = $this->users->findOneBy(['id' => $id]);

		if (!$this->account) {
			$this->account = new Entity\User;
			$this->account->role = self::DEFAULT_ROLE;
		}
	}

	public function render()
	{
		$this->template->setFile(__DIR__ . '/UserEditForm.latte');
		$this->template->render();
	}

	protected function createComponentForm()
	{
		$form = new UI\Form;
		$form->addProtection();
		$form->addText('username', 'Uživatelské přihlašovací jméno:')
			->setDefaultValue($this->account->username)
			->setRequired('Zadejte prosím přihlašovací jméno.');
		$form->addPassword('password', 'Nové heslo k tomuto účtu:')
			->setRequired('Zadejte prosím své stávající, nebo nové heslo.');
		$form->addPassword('passwordVerify', 'Heslo pro kontrolu:')
			->setRequired('Zadejte prosím heslo ještě jednou pro kontrolu')
			->addRule(UI\Form::EQUAL, 'Hesla se neshodují', $form['password']);
		$form->addSelect('role', 'Role:', $this->roles)
			->setDefaultValue($this->account->role);
		$form->addSubmit('save', 'Uložit změny');
		$form->onSuccess[] = $this->formSucceeded;
		return $form;
	}

	public function formSucceeded($_, $vals)
	{
		try {
			$this->account->username = $vals->username;
			$this->account->password = Passwords::hash($vals->password);

			if ($this->presenter->user->isInRole('admin') && isset($vals->role)) {
				$this->account->role = $vals->role;
			} else {
				$this->account->role = self::DEFAULT_ROLE;
			}

			$this->users->save($this->account);
			$this->presenter->flashMessage('Změny úspěšně uloženy.', 'success');
		} catch (Doctrine\DBAL\Exception\UniqueConstraintViolationException $exc) {
			$this->presenter->flashMessage('Uživatel s tímto jménem již existuje. Zvolte prosím jiné.', 'danger');
		} catch (Nette\Security\AuthenticationException $exc) {
			$this->presenter->flashMessage('Myslím to vážně, editovat opravdu **ne**můžete!', 'danger');
			$this->redirect('this');
			return;
		}
		$this->onSave();
	}

}

interface IUserEditFormFactory
{
	/** @return UserEditForm */
	function create($id);
}

<?php

namespace App;

use Nette;
use Nette\Application\UI;

/** Sign in/out presenters. */
class SignPresenter extends BasePresenter {

	/** @persistent */
	public $backlink = '';

	public function actionIn() {
		if ($this->user->isLoggedIn() && $this->user->isAllowed('Admin', 'view')) {
			$this->redirect('Admin:default');
		}
	}

	public function actionOut() {
		$this->getUser()->logout();
		$this->flashMessage('Odhlášení bylo úpěšné.', 'info');
		$this->redirect('in');
	}

	/**
	 * Sign-in form factory.
	 * @return Nette\Application\UI\Form
	 */
	protected function createComponentSignInForm() {
		$form = new UI\Form;
		$form->addText('username', 'Username:')
			->setRequired('Zadejte prosím uživatelské jméno.');
		$form->addPassword('password', 'Password:')
			->setRequired('Zadejte prosím správné heslo.');
		$form->addCheckbox('remember', 'Zapamatovat si přihlášení');
		$form->addSubmit('send', 'Přihlásit se');
		$form->onSuccess[] = $this->signInFormSucceeded;
		return $form;
	}

	public function signInFormSucceeded(UI\Form $form) {
		$values = $form->getValues();
		if ($values->remember) {
			$this->getUser()->setExpiration('+ 14 days', FALSE);
		} else {
			$this->getUser()->setExpiration('+ 20 minutes', TRUE);
		}
		try {
			$this->getUser()->login($values->username, $values->password);
		} catch (Nette\Security\AuthenticationException $e) {
			$form->addError($e->getMessage());
			return;
		}
		$this->restoreRequest($this->backlink);
		$this->redirect('Admin:');
	}

}

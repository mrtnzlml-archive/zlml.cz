<?php

namespace App;
use Model;
use Nette;

/** Sign in/out presenters. */
class SignPresenter extends BasePresenter {

	public function signInFormSucceeded($form) {
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

		$this->redirect('Homepage:');
	}

	public function actionOut() {
		$this->getUser()->logout();
		$this->flashMessage('Odhlášení bylo úpěšné.');
		$this->redirect('in');
	}

	/**
	 * Sign-in form factory.
	 * @return Nette\Application\UI\Form
	 */
	protected function createComponentSignInForm() {
		$form = new Nette\Application\UI\Form;
		$form->addText('username', 'Username:')
			->setRequired('Zadejte prosím uživatelské jméno.');

		$form->addPassword('password', 'Password:')
			->setRequired('Zadejte prosím správné heslo.');

		$form->addCheckbox('remember', 'Keep me signed in');

		$form->addSubmit('send', 'Přihlásit se');

		// call method signInFormSucceeded() on success
		$form->onSuccess[] = $this->signInFormSucceeded;
		return $form;
	}

}

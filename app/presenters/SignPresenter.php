<?php

namespace App;
use Model;
use Nette;

/** Sign in/out presenters. */
class SignPresenter extends BasePresenter {

	public function actionGoogleLogin() {
		$url = $this->context->google->getLoginUrl([
			'scope' => $this->context->parameters['google']['scope'],
			'redirect_uri' => 'http://www.zeminem.cz/oauth2callback', //$this->link('//googleAuth'),
		]);
		$this->redirectUrl($url);
	}

	public function actionGoogleAuth($code, $error = NULL) {
		if ($error) {
			$this->flashMessage('Please allow this application access to your Google account in order to log in.');
			$this->redirect('in');
		}

		$g = $this->context->google;
		$token = $g->getToken($code, $this->link('this'));
		$this->user->googleLogin($g->getInfo($token));
		$this->redirect(':Front:Profile:');
	}

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
		$this->flashMessage('You have been signed out.');
		$this->redirect('in');
	}

	/**
	 * Sign-in form factory.
	 * @return Nette\Application\UI\Form
	 */
	protected function createComponentSignInForm() {
		$form = new Nette\Application\UI\Form;
		$form->addText('username', 'Username:')
			->setRequired('Please enter your username.');

		$form->addPassword('password', 'Password:')
			->setRequired('Please enter your password.');

		$form->addCheckbox('remember', 'Keep me signed in');

		$form->addSubmit('send', 'Sign in');

		// call method signInFormSucceeded() on success
		$form->onSuccess[] = $this->signInFormSucceeded;
		return $form;
	}

}

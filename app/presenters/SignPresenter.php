<?php

namespace App;

use Cntrl\ISignInFactory;
use Nette;
use Nette\Application\UI;

/** Sign in/out presenters. */
class SignPresenter extends BasePresenter
{

	/** @var ISignInFactory @inject */
	public $signInFormFactory;

	public function actionIn()
	{
		if ($this->user->isLoggedIn() && $this->user->isAllowed('Admin:Admin', 'view')) {
			$this->redirect(':Admin:Admin:default');
		}
	}

	public function actionOut()
	{
		$this->getUser()->logout();
		$this->flashMessage('Odhlášení bylo úpěšné.', 'info');
		$this->redirect('in');
	}

	/** @return \Cntrl\SignIn */
	protected function createComponentSignInForm()
	{
		return $this->signInFormFactory->create();
	}

}

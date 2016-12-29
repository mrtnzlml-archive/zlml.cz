<?php declare(strict_types = 1);

namespace App\AuthModule\Presenters;

use App\AuthModule\Components\SignInForm\ISignInFactory;

class SignPresenter extends \App\FrontModule\Presenters\BasePresenter
{

	/** @var ISignInFactory @inject */
	public $signInFormFactory;

	public function actionIn()
	{
		if ($this->user->isLoggedIn() && $this->user->isAllowed('Admin:Admin', 'view')) {
			$this->redirect(':Admin:Admin:default');
		}
	}

	/**
	 * @return \App\AuthModule\Components\SignInForm\SignIn
	 */
	protected function createComponentSignInForm()
	{
		return $this->signInFormFactory->create();
	}

}

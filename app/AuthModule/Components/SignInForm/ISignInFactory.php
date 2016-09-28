<?php declare(strict_types = 1);

namespace App\AuthModule\Components\SignInForm;

interface ISignInFactory
{

	/** @return SignIn */
	public function create();

}

<?php declare(strict_types = 1);

namespace Cntrl;

interface ISignInFactory
{

	/** @return SignIn */
	public function create();

}

<?php declare(strict_types = 1);

namespace App\AdminModule\Components\UserEditForm;

interface IUserEditFormFactory
{

	/** @return UserEditForm */
	public function create($id);

}

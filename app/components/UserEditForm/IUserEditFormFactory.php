<?php declare(strict_types = 1);

namespace Cntrl;

interface IUserEditFormFactory
{

	/** @return UserEditForm */
	public function create($id);

}

<?php declare(strict_types = 1);

namespace App\AdminModule\Components\AdminMenu;

interface IAdminMenuFactory
{

	/** @return AdminMenu */
	public function create();

}

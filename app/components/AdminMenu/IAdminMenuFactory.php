<?php declare(strict_types = 1);

namespace Cntrl;

interface IAdminMenuFactory
{

	/** @return AdminMenu */
	public function create();

}

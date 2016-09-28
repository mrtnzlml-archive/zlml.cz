<?php declare(strict_types = 1);

namespace App\FrontModule\Components\VisualPaginator;

interface IVisualPaginatorFactory
{

	/** @return VisualPaginator */
	public function create();

}

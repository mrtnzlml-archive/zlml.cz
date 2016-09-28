<?php declare(strict_types = 1);

namespace Cntrl;

interface IVisualPaginatorFactory
{

	/** @return VisualPaginator */
	public function create();

}

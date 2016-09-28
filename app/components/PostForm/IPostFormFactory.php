<?php declare(strict_types = 1);

namespace Cntrl;

interface IPostFormFactory
{

	/** @return PostForm */
	public function create($id);

}

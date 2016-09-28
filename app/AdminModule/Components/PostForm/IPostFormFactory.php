<?php declare(strict_types = 1);

namespace App\AdminModule\Components\PostForm;

interface IPostFormFactory
{

	/** @return PostForm */
	public function create($id);

}

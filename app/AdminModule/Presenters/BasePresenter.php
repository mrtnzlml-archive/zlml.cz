<?php declare(strict_types = 1);

namespace App\AdminModule\Presenters;

abstract class BasePresenter extends \App\FrontModule\Presenters\BasePresenter
{

	public function formatLayoutTemplateFiles()
	{
		return [__DIR__ . '/../../FrontModule/Presenters/Templates/@layout.latte'];
	}

}

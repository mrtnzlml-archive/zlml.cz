<?php

namespace App;

use Nette;

class PicturePresenter extends BasePresenter {

	/** @var \IAdminMenuFactory @inject */
	public $adminMenuFactory;

	/**
	 * @return \Cntrl\AdminMenu
	 */
	protected function createComponentAdminMenu() {
		return $this->adminMenuFactory->create();
	}

	public function formatLayoutTemplateFiles() {
		$name = $this->getName();
		$presenter = substr($name, strrpos(':' . $name, ':'));
		$layout = $this->layout ? $this->layout : 'layout';
		$dir = dirname($this->getReflection()->getFileName());
		$dir = is_dir("$dir/templates") ? $dir : dirname($dir);
		$list = [
			"$dir/templates/$presenter/@$layout.latte",
			"$dir/templates/$presenter.@$layout.latte",
			"$dir/templates/$presenter/@$layout.phtml",
			"$dir/templates/$presenter.@$layout.phtml",
		];
		$list[] = WWW_DIR . "/../app/templates/@$layout.latte"; //FIXME
		return $list;
	}

	public function formatTemplateFiles() {
		$dir = dirname($this->getReflection()->getFileName());
		$dir = dirname($dir);
		return [
			"$dir/PictureExtension/$this->view.latte",
		];
	}

}

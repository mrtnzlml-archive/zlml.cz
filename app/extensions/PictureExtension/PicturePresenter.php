<?php

namespace App;

use Nette;

class PicturePresenter extends BasePresenter {

	public function formatLayoutTemplateFiles() {
		$name = $this->getName();
		$presenter = substr($name, strrpos(':' . $name, ':'));
		$layout = $this->layout ? $this->layout : 'layout';
		$dir = dirname($this->getReflection()->getFileName());
		$dir = is_dir("$dir/templates") ? $dir : dirname($dir);
		$list = array(
			"$dir/templates/$presenter/@$layout.latte",
			"$dir/templates/$presenter.@$layout.latte",
			"$dir/templates/$presenter/@$layout.phtml",
			"$dir/templates/$presenter.@$layout.phtml",
		);
		$list[] = WWW_DIR . "/../app/templates/@$layout.latte"; //FIXME
		return $list;
	}

	public function formatTemplateFiles() {
		$dir = dirname($this->getReflection()->getFileName());
		$dir = dirname($dir);
		return array(
			"$dir/PictureExtension/$this->view.latte",
		);
	}

}

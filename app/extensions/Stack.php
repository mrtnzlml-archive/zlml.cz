<?php

class Stack extends Nette\Object {

	private static $stack;

	private $templates = [];
	private $menu = [];

	public static function getStack() {
		if (!self::$stack) {
			self::$stack = new Stack;
		}
		return self::$stack;
	}

	public function addTemplate($template) { $this->templates[] = $template; }
	public function getTemplates() { return $this->templates; }

	public function addMenu($menu) { $this->menu[] = $menu; }
	public function getMenu() { return $this->menu; }

	//TODO: další umístění prvků, které přidávají do blogu nějakou funkcionalitu

}

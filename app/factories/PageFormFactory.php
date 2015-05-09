<?php

class PageFormFactory
{

	private $pages;

	public function __construct(Model\Pages $pages)
	{
		$this->pages = $pages;
	}

	public function create($id)
	{
		return new \Cntrl\PageForm($this->pages, $id);
	}

}

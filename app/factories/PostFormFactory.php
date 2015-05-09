<?php

class PostFormFactory
{

	private $posts;
	private $tags;

	public function __construct(Model\Posts $posts, Model\Tags $tags)
	{
		$this->posts = $posts;
		$this->tags = $tags;
	}

	public function create($id)
	{
		return new \Cntrl\PostForm($this->posts, $this->tags, $id);
	}

}

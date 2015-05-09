<?php

class MenuItem extends Nette\Object
{

	/** @var string */
	private $badge = NULL;
	/** @var string */
	private $heading = NULL;
	/** @var string */
	private $link;
	/** @var string */
	private $text = NULL;

	/**
	 * @param string $badge
	 */
	public function setBadge($badge)
	{
		$this->badge = $badge;
	}

	/**
	 * @return string
	 */
	public function getBadge()
	{
		return $this->badge;
	}

	/**
	 * @param string $heading
	 */
	public function setHeading($heading)
	{
		$this->heading = $heading;
	}

	/**
	 * @return string
	 */
	public function getHeading()
	{
		return $this->heading;
	}

	/**
	 * @param string $link
	 */
	public function setLink($link)
	{
		$this->link = $link;
	}

	/**
	 * @return string
	 */
	public function getLink()
	{
		return $this->link;
	}

	/**
	 * @param string $text
	 */
	public function setText($text)
	{
		$this->text = $text;
	}

	/**
	 * @return string
	 */
	public function getText()
	{
		return $this->text;
	}

}

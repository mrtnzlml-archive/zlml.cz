<?php

class SettingsFormFactory
{

	/** @var \Model\Settings */
	private $settings;

	public function __construct(Model\Settings $settings)
	{
		$this->settings = $settings;
	}

	public function create()
	{
		return new \Cntrl\SettingsForm($this->settings);
	}

}

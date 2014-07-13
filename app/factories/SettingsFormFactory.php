<?php

class SettingsFormFactory {

	/** @var \App\Settings */
	private $settings;

	public function __construct(App\Settings $settings) {
		$this->settings = $settings;
	}

	public function create() {
		return new \Cntrl\SettingsForm($this->settings);
	}

}
<?php

namespace Cntrl;

use Model;
use Entity;
use Kdyby;
use Nette\Application\UI;

class SettingsForm extends UI\Control {

	public $onSave = [];

	/** @var \Model\Settings */
	private $settings;

	public function __construct(Model\Settings $settings) {
		parent::__construct();
		$this->settings = $settings;
	}

	public function render() {
		$this->template->setFile(__DIR__ . '/SettingsForm.latte');
		$this->template->render();
	}

	protected function createComponentSettingsForm() {
		$form = new UI\Form;
		$form->addProtection();
		$form->addGroup('Obecné nastavení');
		$form->addCheckbox('random_search', 'Povolit random výběr příspěvku');
		$form->addCheckbox('show_content', 'Zobrazit obsah blogu');
		$form->addText('ga_code', 'Google Analytics kód');
		$form->addText('disqus_shortname', 'Disqus shortname');
		//nastavení příspěvků (v grupě vedle obecného)

		$form->addGroup('Aktivace rozšíření'); //našítat dostupná rozšíření
		$form->addCheckbox('enable1', 'Aktivovat EXTENSION'); //TODO: do vlastního extension config povolování extensions

		$form->addGroup(NULL);
		$form->addSubmit('save', 'Uložit a publikovat');
		$form->onSuccess[] = $this->settingsFormSucceeded;
		return $form;
	}

	public function settingsFormSucceeded($form) {
		//$this->onBeforeRestrictedFunctionality($this); //FIXME: must be registered in config, but it's against generated factories
		if (!$this->editable()) {
			$this->presenter->flashMessage('Myslím to vážně, editovat opravdu **ne**můžete!', 'danger');
			$this->redirect('this');
			return;
		}
		$vals = $form->getValues();
		try {
			//TODO
		} catch (\Exception $exc) {
			$this->presenter->flashMessage($exc->getMessage(), 'danger');
		}
	}

	private function editable() {
		return $this->presenter->user->isAllowed('Admin', Model\Authorizator::EDIT) ? TRUE : FALSE;
	}

}

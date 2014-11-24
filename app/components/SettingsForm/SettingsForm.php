<?php

namespace Cntrl;

use Entity;
use Kdyby;
use Model;
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
		//Obecné nastavení:
		$form->addCheckbox('random_search', 'Povolit random výběr příspěvků');
		$form->addCheckbox('show_content', 'Zobrazit obsah blogu');
		$form->addText('ga_code', 'Google Analytics kód:');
		$form->addText('disqus_shortname', 'Disqus shortname:');
		//Nastavení příspěvků:
		$form->addCheckbox('show_comments', 'Zobrazovat komentáře');
		$form->addCheckbox('show_print', 'Umožnit tisk článků');

		//$form->addCheckbox('enable1', 'Aktivovat EXTENSION'); //TODO: do vlastního extension config povolování extensions

		$form->defaults = $this->settings->findAllByKeys();
		$form->addSubmit('save', 'Uložit změny');
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
		try {
			$vals = $form->getValues();
			$this->settings->save($vals);
			$this->presenter->flashMessage('Změny jsou úspěšně uloženy.', 'success');
		} catch (\Exception $exc) {
			$this->presenter->flashMessage($exc->getMessage(), 'danger');
		}
		$this->onSave();
	}

	private function editable() {
		return $this->presenter->user->isAllowed('Admin', Model\Authorizator::CREATE) ? TRUE : FALSE;
	}

}

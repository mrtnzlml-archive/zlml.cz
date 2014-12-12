<?php

namespace Cntrl;

use Entity;
use Kdyby;
use Model;
use Nette;
use Nette\Application\UI;

class SettingsForm extends UI\Control {

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
		$form->addCheckbox('disable_blog', 'Zakázat blog jako takový');
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

	public function settingsFormSucceeded(UI\Form $form, Nette\Utils\ArrayHash $vals) {
		try {
			$this->settings->save($vals);
			$this->presenter->flashMessage('Změny jsou úspěšně uloženy.', 'success');
		} catch (Nette\Security\AuthenticationException $exc) {
			$this->presenter->flashMessage('Myslím to vážně, editovat opravdu **ne**můžete!', 'danger');
			$this->redirect('this');
			return;
		}
	}

}

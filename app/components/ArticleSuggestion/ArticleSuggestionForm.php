<?php

namespace Cntrl;

use Entity;
use Kdyby;
use Model;
use Nette;
use Nette\Application\UI;
use Tracy\Debugger;

class ArticleSuggestion extends UI\Control {

	public function __construct() {
		parent::__construct();
	}

	public function render() {
		$this->template->setFile(__DIR__ . '/ArticleSuggestionForm.latte');
		$this->template->render();
	}

	protected function createComponentArticleSuggestion() {
		$form = new UI\Form;
		$form->addTextArea('article', NULL)->setRequired('Vyplňte prosím, o čem by měl článek být.');
		$form->addSubmit('send', 'Odeslat návrh');
		$form->onSuccess[] = $this->articleSuggestionSucceeded;
		return $form;
	}

	/**
	 * @param UI\Form $form
	 * @param $vals
	 */
	public function articleSuggestionSucceeded(UI\Form $form, $vals) {
		try {
			$mailer = new Nette\Mail\SendmailMailer;
			$message = new Nette\Mail\Message();
			$message->setFrom('postmaster@zeminem.cz')
				->addTo('mrtnzlml@gmail.com')
				->setSubject('Návrh článku - zeminem.cz')
				->setHtmlBody("<h1>Návrh článku</h1> $vals->article");
			$mailer->send($message);
		} catch (Nette\InvalidStateException $exc) { //We have fallback message backup... (-:
			Debugger::log('InvalidStateException: ' . $exc->getMessage(), Debugger::CRITICAL);
			Debugger::log('Message BACKUP: ' . json_encode($vals), Debugger::CRITICAL);
		}
		$tmp = Nette\Utils\Html::el('strong', 'Výborně!');
		$this->presenter->flashMessage("$tmp Návrh článku byl odeslán&hellip;", 'success');
		$this->redirect('this');
	}

}

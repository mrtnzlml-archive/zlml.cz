<?php

namespace Cntrl;

use Entity;
use Kdyby;
use Model;
use Nette\Application\UI;

class PageForm extends UI\Control {

	public $onSave = [];
	//public $onBeforeRestrictedFunctionality = [];

	/** @var \Model\Pages */
	private $pages;
	private $page;

	public function __construct(Model\Pages $pages, $id) {
		parent::__construct();
		$this->pages = $pages;
		$this->page = $this->pages->findOneBy(['id' => $id]);
	}

	public function render() {
		$this->template->setFile(__DIR__ . '/PageForm.latte');
		$this->template->render();
	}

	protected function createComponentPageForm() {
		$form = new UI\Form;
		$form->addProtection();
		$form->addText('title', 'Název:')->setRequired('Je zapotřebí vyplnit název stránky.');
		$form->addText('slug', 'URL slug:')->setRequired('Je zapotřebí vyplnit slug.');
		$form->addTextArea('editor', 'Obsah stránky:')
			->setHtmlId('editor')
			->setRequired('Je zapotřebí napsat nějaký text.');
		if ($this->page) {
			$form->setDefaults([
				'title' => $this->page->title,
				'slug' => $this->page->slug,
				'editor' => $this->page->body,
			]);
		}
		$form->addSubmit('save', 'Uložit a publikovat');
		$form->onSuccess[] = $this->pageFormSucceeded;
		return $form;
	}

	public function pageFormSucceeded($form) {
		//$this->onBeforeRestrictedFunctionality($this); //FIXME: must be registered in config, but it's against generated factories
		if (!$this->editable()) {
			$this->presenter->flashMessage('Myslím to vážně, editovat opravdu **ne**můžete!', 'danger');
			$this->redirect('this');
			return;
		}
		$vals = $form->getValues();
		try {
			if (!$this->page) {
				$this->page = new Entity\Page();
				$this->page->date = new \DateTime();
			}
			$this->page->title = $vals->title;
			$this->page->slug = $vals->slug;
			$this->page->body = $vals->editor;
			$this->page->draft = FALSE;
			$this->pages->save($this->page);
			$this->presenter->flashMessage('Stránka byla úspěšně uložena a publikována.', 'success');
			$this->onSave();
		} catch (Kdyby\Doctrine\DuplicateEntryException $exc) { //DBALException
			$this->presenter->flashMessage('Tento URL slug je již v databázi uložen, zvolte prosím jiný.', 'danger');
		}
	}

	private function editable() {
		return $this->presenter->user->isAllowed('Admin', Model\Authorizator::CREATE) ? TRUE : FALSE;
	}

}

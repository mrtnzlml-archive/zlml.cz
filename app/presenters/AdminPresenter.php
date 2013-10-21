<?php

namespace App;

use Nette;

class AdminPresenter extends BasePresenter {

	/** @var \Model\Posts @inject */
	public $posts;

	/** @persistent */
	public $id = NULL;

	private $value;

	public function startup() {
		parent::startup();
		if (!$this->user->isLoggedIn()) {
			$this->flashMessage('Tudy cesta nevede!', 'alert-error');
			$this->redirect('Sign:in');
		}
	}

	public function renderDefault($id) {
		$this->template->tags = $this->posts->getAllTags();
		if ($id != NULL) {
			$this->id = $id;
			$this->value = $this->posts->getPostByID($id);
			$this->template->editace = $id;
		}
	}

	public function renderPrehled() {
		$this->template->posts = $this->posts->getAllPosts()->order('date DESC');
	}

	public function renderTags() {
		$this->template->tags = $this->posts->getAllTags();
	}

	protected function createComponentNewPost() {
		$form = new Nette\Application\UI\Form;
		$form->addProtection();
		$form->addText('title', 'Titulek:')
			->setAttribute('class', 'form-control title')
			->setValue(empty($this->value) ? '' : $this->value->title)
			->setRequired('Je zapotřebí vyplnit titulek.');
		$tags = array();
		if($this->id) {
			foreach($this->posts->getPostByID($this->id)->related('tags') as $a) {
				$tags[] = $this->posts->getTagByID($a->tag_id)->name;
			}
		}
		$form->addText('tags', 'Tagy:')
			->setAttribute('class', 'form-control tags')
			->setValue(implode(', ', $tags));
		$form->addTextArea('editor', 'Body:')
			->setHtmlId('editor')
			->setValue(empty($this->value) ? '' : $this->value->body)
			->setRequired('Je zapotřebí napsat nějaký text.');
		//Alternative: http://tarruda.github.io/bootstrap-datetimepicker/
		$form->addText('release', 'Datum zveřejnění:')
			->setType('datetime-local'); //HTML5!
		$form->addSubmit('save', 'Uložit a publikovat');
		$form->onSuccess[] = $this->processPostSucceeded;
		return $form;
	}

	public function processPostSucceeded($form) {
		$vals = $form->getValues();
		if ($this->id) { // upravujeme záznam
			try {
				$this->posts->updatePost($vals->title, array_unique(explode(', ', $vals->tags)), $vals->editor, $vals->release, $this->id);
				$this->flashMessage('Příspěvek byl úspěšně uložen a publikován.', 'alert-success');
				$this->redirect('this');
			} catch (\PDOException $exc) {
				$this->flashMessage($exc->getMessage(), 'alert-error');
			}
		} else { // přidáváme záznam
			try {
				$this->posts->newPost($vals->title, array_unique(explode(', ', $vals->tags)), $vals->editor, $vals->release);
				$this->flashMessage('Příspěvek byl úspěšně uložen a publikován.', 'alert-success');
				$this->redirect('this');
			} catch (\PDOException $exc) {
				$this->flashMessage($exc->getMessage(), 'alert-error');
			}
		}
	}

	public function handleUpdate($title, $body, $tags) {
		//TODO: nefunguje, proč?
		/*$texy = new \fshlTexy();
		$texy->addHandler('block', array($texy, 'blockHandler'));
		$texy->tabWidth = 4;
		$texy->headingModule->top = 3; //start at H3
		$this->template->preview = $texy->process($body);
		$this->template->title = $title;
		$this->template->tagsPrew = array_unique(explode(', ', $tags));
		if ($this->isAjax()) {
			$this->invalidateControl('preview');
		}*/
		$this->template->preview = "Náhled";
		$this->template->title = "Titulek";
		if ($this->isAjax()) {
			$this->invalidateControl('preview');
		}
	}

	public function handleDelete($id) {
		try {
			$this->posts->deletePostByID($id);
			$this->flashMessage('Post byl úspěšně smazán.', 'alert-success');
		} catch (\Exception $exc) {
			$this->flashMessage($exc->getMessage(), 'alert-error');
		}
		$this->redirect('this');
	}

	public function handleDeleteTag($tag_id) {
		try {
			$this->posts->deleteTagById($tag_id);
			$this->flashMessage('Tag byl úspěšně smazán.', 'alert-success');
		} catch (\Exception $exc) {
			$this->flashMessage($exc->getMessage(), 'alert-error');
		}
		$this->redirect('this');
	}

	public function handleRegenerate($tag_id) {
		$color = substr(md5(rand()), 0, 6); //Short and sweet
		try {
			$this->posts->updateTagByID($tag_id, array('color' => $color));
			$this->redirect('this');
		} catch(\PDOException $exc) {
			$this->flashMessage($exc->getMessage(), 'alert-error');
			$this->redirect('this');
		}
	}

}
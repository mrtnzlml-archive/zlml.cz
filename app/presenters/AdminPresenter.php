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
		//TODO: bez ->where('release_date < NOW()'); !
		$this->template->posts = $this->posts->getAllPosts()->order('date DESC');
	}

	public function renderTags() {
		$this->template->tags = $this->posts->getAllTags();
	}

	protected function createComponentNewPost() {
		$form = new Nette\Application\UI\Form;
		$form->addProtection();
		$form->addText('title', 'Titulek:')
			->setValue(empty($this->value) ? '' : $this->value->title)
			->setRequired('Je zapotřebí vyplnit titulek.');
		$form->addText('slug', 'URL slug:')
			->setValue(empty($this->value) ? '' : $this->value->slug)
			->setRequired('Je zapotřebí vyplnit slug.');
		$tags = array();
		if($this->id) {
			foreach($this->posts->getPostByID($this->id)->related('tags') as $a) {
				$tags[] = $this->posts->getTagByID($a->tag_id)->name;
			}
		}
		$form->addText('tags', 'Tagy:')
			->setAttribute('class', 'form-control')
			->setValue(implode(', ', $tags));
		$form->addTextArea('editor', 'Body:')
			->setHtmlId('editor')
			->setValue(empty($this->value) ? '' : $this->value->body)
			->setRequired('Je zapotřebí napsat nějaký text.');
		$form->addText('release', 'Datum zveřejnění:')
			->setType('datetime-local'); //HTML5
		$form->addSubmit('save', 'Uložit a publikovat');
		$form->onSuccess[] = $this->processPostSucceeded;
		return $form;
	}

	public function processPostSucceeded($form) {
		$vals = $form->getValues();
		if ($this->id) { // upravujeme záznam
			try {
				$this->posts->sf->connection->beginTransaction();
				$this->posts->updatePost($vals->title, $vals->slug, array_unique(explode(', ', $vals->tags)), $vals->editor, $vals->release, $this->id);
				$this->flashMessage('Příspěvek byl úspěšně uložen a publikován.', 'alert-success');
				$this->posts->sf->connection->commit();
				$this->redirect('this');
			} catch (\PDOException $exc) {
				$this->posts->sf->connection->rollBack();
				$this->flashMessage($exc->getMessage(), 'alert-error');
			}
		} else { // přidáváme záznam
			try {
				$this->posts->sf->connection->beginTransaction();
				$this->posts->newPost($vals->title, $vals->slug, array_unique(explode(', ', $vals->tags)), $vals->editor, $vals->release);
				$this->flashMessage('Příspěvek byl úspěšně uložen a publikován.', 'alert-success');
				$this->posts->sf->connection->commit();
				$this->redirect('this');
			} catch (\PDOException $exc) {
				$this->posts->sf->connection->rollBack();
				$this->flashMessage($exc->getMessage(), 'alert-error');
			}
		}
	}

	public function handleUpdate($title, $content, $tags) {
		$texy = new \fshlTexy();
		$texy->addHandler('block', array($texy, 'blockHandler'));
		$texy->tabWidth = 4;
		$texy->headingModule->top = 3; //start at H3
		$this->template->preview = Nette\Utils\Html::el()->setHtml($texy->process($content));
		$this->template->title = $title;
		$this->template->tagsPrew = array_unique(explode(', ', $tags));
		if ($this->isAjax()) {
			$this->invalidateControl('preview');
		} else {
			$this->redirect('this');
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
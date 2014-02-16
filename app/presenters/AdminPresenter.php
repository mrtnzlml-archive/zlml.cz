<?php

namespace App;

use Cntrl;
use Entity;
use Kdyby;
use Nette;

class AdminPresenter extends BasePresenter {

	/** @var Tags @inject */
	public $tags;

	/** @persistent */
	public $id = NULL;

	private $value;

	public function startup() {
		parent::startup();
		if (!$this->user->isLoggedIn()) {
			$this->flashMessage('Tudy cesta nevede!', 'alert-danger');
			$this->redirect('Sign:in');
		}
	}

	public function beforeRender() {
		parent::beforeRender();
		$this->template->tagcount = $this->tags->countBy();
	}

	public function actionDefault($id) {
		//$post = $this->posts->findOneBy(['id' => $id]);
		//TODO: array
		//$this['newPost']->setDefaults($post);
	}

	public function renderDefault($id) {
		$this->template->tags = $this->tags->findBy(array());
		if ($id != NULL) {
			$this->id = $id;
			$this->value = $this->posts->findOneBy(['id' => $id]);
			$this->template->editace = $id;
		}
	}

	public function renderPrehled() {
		$this->template->posts = $this->posts->findBy(array(), ['date' => 'DESC']);
	}

	public function renderTags() {
		$this->template->tags = $this->tags->findBy(array());
	}

	protected function createComponentColor() {
		$form = new Nette\Application\UI\Form;
		$form->addProtection();
		foreach ($this->tags->findBy(array()) as $tag) {
			$form->addText('color' . $tag->id)
				->setType('color')
				->setValue('#' . $tag->color);
			$form->addSubmit('update' . $tag->id, 'Změnit barvu')
				->onClick[] = function ($this) use ($tag) {
				$this->colorSucceeded($this, $tag->id);
			};
		}
		return $form;
	}

	/**
	 * @param $button
	 * @param $id
	 */
	public function colorSucceeded($button, $id) {
		$vals = $button->getForm()->getValues();
		$newColor = preg_replace('<#>', '', $vals['color' . $id]);
		try {
			$this->posts->updateTagByID($id, array(
				'color' => $newColor,
			));
			$this->flashMessage('Tag byl úspěšně aktualizován.', 'alert-success');
		} catch (\Exception $exc) {
			$this->flashMessage($exc->getMessage(), 'alert-danger');
		}
		$this->redirect('this');
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
		//TODO - rovnou naplnit viz http://doc.nette.org/cs/2.1/quickstart/creating-posts#toc-uprava-prispevku
		$tags = array();
		if ($this->id) {
			foreach ($this->posts->findOneBy(['id' => $this->id])->tags as $tag) {
				$tags[] = $tag->name;
			}
		}
		$form->addText('tags', 'Tagy:')
			->setAttribute('class', 'form-control')
			->setValue(implode(', ', $tags));
		$form->addTextArea('editor', 'Body:')
			->setHtmlId('editor')
			->setValue(empty($this->value) ? '' : $this->value->body)
			->setRequired('Je zapotřebí napsat nějaký text.');
		$form['release'] = new Cntrl\DateInput('Datum zveřejnění (den | měsíc | rok):');
		$form['release']->setDefaultValue(new \DateTime);
		$form->addSubmit('save', 'Uložit a publikovat');
		$form->onSuccess[] = $this->processPostSucceeded;
		return $form;
	}

	public function processPostSucceeded($form) {
		$vals = $form->getValues();
		$id = $this->getParameter('id');
		try {
			if ($id) { // upravujeme záznam
				$post = $this->posts->findOneBy(['id' => $id]);
			} else { // přidáváme záznam
				$post = new Entity\Post();
			}
			$post->title = $vals->title;
			$post->slug = $vals->slug;
			$post->body = $vals->editor;
			$post->date = new \DateTime();
			$post->release_date = new \DateTime();
			foreach (array_unique(explode(', ', $vals->tags)) as $tag_name) {
				$tag = new Entity\Tag();
				$tag->name = $tag_name;
				$tag->color = substr(md5(rand()), 0, 6); //Short and sweet
				$post->addTag($tag); //FIXME
			}
			$this->posts->save($post);
			$this->flashMessage('Příspěvek byl úspěšně uložen a publikován.', 'alert-success');
			$this->redirect('this');
		} catch (Kdyby\Doctrine\DuplicateEntryException $exc) { //DBALException
			$this->flashMessage($exc->getMessage(), 'alert-danger');
		}
	}

	public function handleUpdate($title, $content, $tags) {
		$texy = new \fshlTexy();
		$texy->addHandler('block', array($texy, 'blockHandler'));
		$texy->tabWidth = 4;
		$texy->headingModule->top = 3; //start at H3
		$this->template->preview = Nette\Utils\Html::el()->setHtml($texy->process($content));
		$this->template->title = $title;
		$this->template->tagsPrev = array_unique(explode(', ', $tags));
		if ($this->isAjax()) {
			$this->redrawControl('preview');
		} else {
			$this->redirect('this');
		}
	}

	public function handleDelete($id) {
		try {
			$this->posts->deletePostByID($id);
			$this->flashMessage('Post byl úspěšně smazán.', 'alert-success');
		} catch (\Exception $exc) {
			$this->flashMessage($exc->getMessage(), 'alert-danger');
		}
		$this->redirect('this');
	}

	public function handleDeleteTag($tag_id) {
		try {
			$this->posts->deleteTagById($tag_id);
			$this->flashMessage('Tag byl úspěšně smazán.', 'alert-success');
		} catch (\Exception $exc) {
			$this->flashMessage($exc->getMessage(), 'alert-danger');
		}
		$this->redirect('this');
	}

	public function handleRegenerate($tag_id) {
		$color = substr(md5(rand()), 0, 6); //Short and sweet
		try {
			$this->posts->updateTagByID($tag_id, array('color' => $color));
			$this->redirect('this');
		} catch (\PDOException $exc) {
			$this->flashMessage($exc->getMessage(), 'alert-danger');
			$this->redirect('this');
		}
	}

}
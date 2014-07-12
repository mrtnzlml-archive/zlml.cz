<?php

namespace Cntrl;

use App;
use Entity;
use Kdyby;
use Nette\Application\UI;

class PostForm extends UI\Control {

	public $onSave = [];
	//public $onBeforeRestrictedFunctionality = [];

	/** @var \App\Posts */
	private $posts;
	/** @var \App\Tags */
	private $tags;
	private $post;

	public function __construct(App\Posts $posts, App\Tags $tags, $id) {
		parent::__construct();
		$this->posts = $posts;
		$this->tags = $tags;
		$this->post = $this->posts->findOneBy(['id' => $id]);
	}

	public function render() {
		$this->template->setFile(__DIR__ . '/PostForm.latte');
		$this->template->render();
	}

	protected function createComponentPostForm() {
		$form = new UI\Form;
		$form->addProtection();
		$form->addText('title', 'Titulek:')->setRequired('Je zapotřebí vyplnit titulek.');
		$form->addText('slug', 'URL slug:')->setRequired('Je zapotřebí vyplnit slug.');
		$tags = array();
		if ($this->post) {
			foreach ($this->post->tags as $tag) {
				$tags[] = $tag->name;
			}
		}
		$form->addText('tags', 'Tagy (oddělené čárkou):')
			->setAttribute('class', 'form-control')
			->setValue(implode(', ', $tags));
		$form->addTextArea('editor', 'Obsah článku:')
			->setHtmlId('editor')
			->setRequired('Je zapotřebí napsat nějaký text.');
		if ($this->post) {
			$form->setDefaults(array(
				'title' => $this->post->title,
				'slug' => $this->post->slug,
				'editor' => $this->post->body,
			));
		}
		$form->addSubmit('save', 'Uložit a publikovat');
		$form->onSuccess[] = $this->postFormSucceeded;
		return $form;
	}

	public function postFormSucceeded($form) {
		//$this->onBeforeRestrictedFunctionality($this); //FIXME: must be registered in config, but it's against generated factories
		if (!$this->editable()) {
			$this->presenter->flashMessage('Myslím to vážně, editovat opravdu **ne**můžete!', 'danger');
			$this->redirect('this');
			return;
		}
		$vals = $form->getValues();
		try {
			if (!$this->post) {
				$this->post = new Entity\Post();
				$this->post->date = new \DateTime();
			}
			$this->post->title = $vals->title;
			$this->post->slug = $vals->slug;
			$this->post->body = $vals->editor;
			$this->post->draft = FALSE;
			foreach (array_unique(explode(', ', $vals->tags)) as $tag_name) {
				$tag = $this->tags->findOneBy(['name' => $tag_name]);
				if (!$tag) {
					$tag = new Entity\Tag();
					$tag->name = $tag_name;
					$tag->color = substr(md5(rand()), 0, 6); //Short and sweet
				}
				if (!empty($tag_name)) {
					$this->post->addTag($tag);
				}
			}
			$this->posts->save($this->post);
			$this->presenter->flashMessage('Příspěvek byl úspěšně uložen a publikován.', 'success');
			$this->onSave();
		} catch (Kdyby\Doctrine\DuplicateEntryException $exc) { //DBALException
			$this->presenter->flashMessage('Tento URL slug je již v databázi uložen, zvolte prosím jiný.', 'danger');
		}
	}

	private function editable() {
		return $this->presenter->user->isAllowed('Admin', App\Authorizator::EDIT) ? TRUE : FALSE;
	}

}

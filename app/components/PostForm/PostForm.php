<?php

namespace Cntrl;

use Entity;
use Kdyby;
use Model;
use Nette;
use Nette\Application\UI;

class PostForm extends UI\Control {

	public $onSave = [];
	//public $onBeforeRestrictedFunctionality = [];

	/** @var \Model\Posts */
	private $posts;
	/** @var \Model\Tags */
	private $tags;
	private $post;

	public function __construct(Model\Posts $posts, Model\Tags $tags, $id) {
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
		$tags = [];
		if ($this->post) {
			foreach ($this->post->tags as $tag) {
				$tags[] = $tag->name;
			}
		}
		$form->addText('tags', 'Tagy (oddělené čárkou):')
			->setAttribute('class', 'form-control')
			->setValue(implode(', ', $tags));
		$form->addText('publish_date', 'Datum publikování článku:')->setType('datetime-local');
		$form->addTextArea('editor', 'Obsah článku:')
			->setHtmlId('editor')
			->setRequired('Je zapotřebí napsat nějaký text.');
		$form->addCheckbox('disable_comments', 'Zakázat pod tímto článkem komentáře');
		if ($this->post) {
			$form->setDefaults([
				'title' => $this->post->title,
				'slug' => $this->post->slug,
				'editor' => $this->post->body,
				'publish_date' => $this->post->publish_date->format('Y-m-d\TH:i:s'),
				'disable_comments' => $this->post->disable_comments,
			]);
		}
		$form->addSubmit('save', 'Uložit a publikovat');
		$form->onSuccess[] = $this->postFormSucceeded;
		return $form;
	}

	public function postFormSucceeded(UI\Form $form, Nette\Utils\ArrayHash $vals) {
		try {
			if (!$this->post) {
				$this->post = new Entity\Post();
				$this->post->date = new \DateTime();
			}
			$this->post->publish_date = $vals->publish_date ? new \DateTime($vals->publish_date) : new \DateTime('now');
			$this->post->title = $vals->title;
			$this->post->slug = $vals->slug;
			$this->post->body = $vals->editor;
			$this->post->disable_comments = $vals->disable_comments;
			$this->post->draft = FALSE;
			foreach (array_unique(preg_split('/\s*,\s*/', $vals->tags)) as $tag_name) {
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
		} catch (Nette\Security\AuthenticationException $exc) {
			$this->presenter->flashMessage('Myslím to vážně, editovat opravdu **ne**můžete!', 'danger');
			$this->redirect('this');
			return;
		}
	}

}

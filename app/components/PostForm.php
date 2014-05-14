<?php

namespace Cntrl;

use App;
use Entity;
use Kdyby;
use Nette\Application\UI;

class PostForm extends UI\Control {

	public $onSave = [];

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
		//FIXME: doesn't work yet
		/*if (!$this->editable()) {
			$this->flashMessage('Myslím to vážně, editovat opravdu **ne**můžete!', 'danger');
			$this->redirect('this');
			return;
		}*/
		$vals = $form->getValues();
		try {
			if ($this->post) { // upravujeme záznam
				$post = $this->post;
			} else { // přidáváme záznam
				//TODO: send pingbacks
				$post = new Entity\Post();
				$post->date = new \DateTime();
			}
			$post->title = $vals->title;
			$post->slug = $vals->slug;
			$post->body = $vals->editor;
			foreach (array_unique(explode(', ', $vals->tags)) as $tag_name) {
				$tag = $this->tags->findOneBy(['name' => $tag_name]);
				if (!$tag) {
					$tag = new Entity\Tag();
					$tag->name = $tag_name;
					$tag->color = substr(md5(rand()), 0, 6); //Short and sweet
				}
				if (!empty($tag_name)) {
					$post->addTag($tag);
				}
			}
			$this->posts->save($post);
			$this->presenter->flashMessage('Příspěvek byl úspěšně uložen a publikován.', 'success');
		} catch (Kdyby\Doctrine\DuplicateEntryException $exc) { //DBALException
			$this->presenter->flashMessage($exc->getMessage(), 'danger');
		}
		$this->onSave();
	}

}
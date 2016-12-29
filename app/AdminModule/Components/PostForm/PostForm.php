<?php declare(strict_types = 1);

namespace App\AdminModule\Components\PostForm;

use App\Posts\Entities\Post;
use App\Posts\Posts;
use App\Tags\Entities\Tag;
use App\Tags\Tags;
use Nette;
use Nette\Application\UI;

class PostForm extends UI\Control
{

	public $onSave = [];

	/** @var Posts */
	private $posts;

	/** @var Tags */
	private $tags;

	private $post;

	public function __construct($id, Posts $posts, Tags $tags)
	{
		parent::__construct();
		$this->posts = $posts;
		$this->tags = $tags;
		$this->post = $this->posts->findOneBy(['id' => $id]);
	}

	public function render()
	{
		$this->template->setFile(__DIR__ . '/PostForm.latte');
		$this->template->render();
	}

	protected function createComponentPostForm()
	{
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
			->setHtmlId('editor');
			//Cannot be required because of CodeMirror - https://github.com/codemirror/CodeMirror-v1/issues/59
			//->setRequired('Je zapotřebí napsat nějaký text.');
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
		$form->onSuccess[] = [$this, 'postFormSucceeded'];
		return $form;
	}

	public function postFormSucceeded(UI\Form $form, Nette\Utils\ArrayHash $vals)
	{
		try {
			if (!$this->post) {
				$this->post = new Post;
				$this->post->date = new \DateTime();
			}
			$this->post->publish_date = $vals->publish_date ? new \DateTime($vals->publish_date) : new \DateTime('now');
			$this->post->title = $vals->title;
			$this->post->slug = $vals->slug;
			$this->post->body = $vals->editor;
			$this->post->disable_comments = $vals->disable_comments;
			$this->post->draft = FALSE;
			foreach (array_unique(preg_split('/\s*,\s*/', $vals->tags)) as $tagName) {
				$tag = $this->tags->findOneBy(['name' => $tagName]);
				if (!$tag) {
					$tag = new Tag;
					$tag->name = $tagName;
					$tag->color = substr(md5('' . mt_rand()), 0, 6); //Short and sweet
				}
				if (!empty($tagName)) {
					$this->post->addTag($tag);
				}
			}
			$this->posts->save($this->post);
			$this->presenter->flashMessage('Příspěvek byl úspěšně uložen a publikován.', 'success');
			$this->onSave();
		} catch (\Doctrine\DBAL\Exception\UniqueConstraintViolationException $exc) {
			$this->presenter->flashMessage('Tento URL slug je již v databázi uložen, zvolte prosím jiný.', 'danger');
		} catch (\Nette\Security\AuthenticationException $exc) {
			$this->presenter->flashMessage('Myslím to vážně, editovat opravdu **ne**můžete!', 'danger');
			$this->redirect('this');
			return;
		}
	}

}

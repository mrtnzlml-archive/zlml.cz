<?php declare(strict_types = 1);

namespace App;

use Cntrl\ISignInFactory;
use Model;
use Nette;

class SinglePresenter extends BasePresenter
{

	/** @var ISignInFactory @inject */
	public $signInFormFactory;

	/** @var \Model\Tags @inject */
	public $tags;

	public function renderObsah()
	{
		$articles = $this->posts->findBy(['publish_date <=' => new \DateTime()], ['title' => 'ASC']);
		$this->template->articles = $articles;

		$letters = [];
		foreach ($articles as $article) {
			$letter = mb_strtoupper(mb_substr($article->title, 0, 1, 'utf-8'));
			$letters[$letter] = $letter;
		}
		$this->template->letters = $letters;
	}

	public function renderArticle($slug)
	{
		$webalized = Nette\Utils\Strings::webalize($slug);
		if (empty($webalized)) {
			$this->redirect('Homepage:default');
		}
		if ($slug !== $webalized) {
			$this->redirect('Single:article', $webalized);
		}
		$post = $this->posts->findOneBy(['slug' => $webalized, 'publish_date <=' => new \DateTime()]); // zobrazení článku podle slugu
		if (!$post) { // pokud článek neexistuje (FALSE), pak forward - about, reference, atd...
			$this->forward($webalized);
		} elseif ($post) { // zobrazení klasických článků
			$texy = $this->prepareTexy();
			$this->template->post = $post;
			$this->template->body = $texy->process($post->body);
		}
	}

	/** @return \Cntrl\SignIn */
	protected function createComponentSignInForm()
	{
		return $this->signInFormFactory->create();
	}

	public function handleGetEmail()
	{
		if ($this->isAjax()) {
			$hidden = 'mrtnzlml@gmail.com'; //TODO: mrtn@zlml.cz
			$el = Nette\Utils\Html::el('a target=_blank')->href('mailto:' . $hidden)->setText($hidden);
			$this->payload->emailLink = (string)$el;
			$this->sendPayload();
		}
		$this->redirect('this');
	}

}

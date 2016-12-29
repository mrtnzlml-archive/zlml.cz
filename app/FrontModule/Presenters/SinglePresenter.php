<?php declare(strict_types = 1);

namespace App\FrontModule\Presenters;

use App\AuthModule\Components\SignInForm\ISignInFactory;
use App\Posts\Posts;
use Nette;

class SinglePresenter extends BasePresenter
{

	/** @var Posts */
	private $posts;

	/** @var ISignInFactory */
	private $signInFormFactory;

	public function __construct(Posts $posts, ISignInFactory $signInFactory)
	{
		parent::__construct();
		$this->posts = $posts;
		$this->signInFormFactory = $signInFactory;
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
		$post = $this->posts->findOneBy([
			'slug' => $webalized,
			'publish_date <=' => new \DateTime(),
		]); // zobrazení článku podle slugu
		if (!$post) { // pokud článek neexistuje (FALSE), pak forward - about, reference, atd...
			$this->forward($webalized);
		} elseif ($post) { // zobrazení klasických článků
			$texy = $this->prepareTexy();
			$this->template->post = $post;
			$this->template->body = $texy->process($post->body);
		}
	}

	/**
	 * @return \App\AuthModule\Components\SignInForm\SignIn
	 */
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

<?php declare(strict_types = 1);

namespace App\FrontModule\Presenters;

use App\Posts\Posts;

class ArchivePresenter extends BasePresenter
{

	/**
	 * @var \App\Posts\Posts
	 */
	private $posts;

	public function __construct(Posts $posts)
	{
		parent::__construct();
		$this->posts = $posts;
	}

	public function renderDefault()
	{
		$posts = $this->posts->findBy(
			['publish_date <=' => new \DateTime()],
			['date' => 'DESC']
		);
		$this->template->posts = $posts;
	}

}

<?php declare(strict_types = 1);

namespace App\FrontModule\Presenters;

use App\Posts\Posts;
use App\Tags\Tags;

class ArchivePresenter extends BasePresenter
{

	/**
	 * @var \App\Posts\Posts
	 */
	private $posts;

	/**
	 * @var \App\Tags\Tags
	 */
	private $tags;

	public function __construct(Posts $posts, Tags $tags)
	{
		parent::__construct();
		$this->posts = $posts;
		$this->tags = $tags;
	}

	public function renderDefault()
	{
		$posts = $this->posts->findBy(
			['publish_date <=' => new \DateTime()],
			['date' => 'DESC']
		);
		$this->template->posts = $posts;
	}

	public function renderTags()
	{
		$tags = $this->tags->findBy(
			[],
			['name' => 'ASC']
		);
		$this->template->tags = $tags;
	}

}

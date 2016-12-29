<?php declare(strict_types = 1);

namespace App\FrontModule\Presenters;

use App\Posts\Posts;
use App\Social\Twitter\Twitter;

class HomepagePresenter extends BasePresenter
{

	/**
	 * @var \App\Social\Twitter\Twitter
	 */
	private $twitter;

	/**
	 * @var \App\Posts\Posts
	 */
	private $posts;

	public function __construct(Twitter $twitter, Posts $posts)
	{
		parent::__construct();
		$this->twitter = $twitter;
		$this->posts = $posts;
	}

	public function renderDefault()
	{
		$this->template->twitterProfile = $this->twitter->getPersonalProfileInfo();
	}

	public function renderRss()
	{
		$this->template->posts = $this->posts->findBy(['publish_date <=' => new \DateTime()], ['date' => 'DESC'], 50);
	}

	public function renderSitemap()
	{
		$this->template->sitemap = $this->posts->findBy(['publish_date <=' => new \DateTime()]);
	}

}

<?php declare(strict_types = 1);

namespace App\FrontModule\Presenters;

use App\FrontModule\Components\VisualPaginator\VisualPaginator;

class HomepagePresenter extends BasePresenter
{

	public function renderDefault()
	{
		$vp = new VisualPaginator($this, 'paginator');
		$paginator = $vp->getPaginator();
		$paginator->itemsPerPage = 10;
		$paginator->itemCount = ITEMCOUNT; //see RouterFactory.php
		$posts = $this->posts->findBy(['publish_date <=' => new \DateTime()], ['date' => 'DESC'], $paginator->itemsPerPage, $paginator->offset);
		$this->template->posts = $posts;
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

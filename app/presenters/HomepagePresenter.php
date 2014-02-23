<?php

namespace App;

use Nette;

class HomepagePresenter extends BasePresenter {

	public function renderDefault() {
		$vp = new \VisualPaginator($this, 'paginator');
		$paginator = $vp->getPaginator();
		$paginator->itemsPerPage = 8;
		$paginator->itemCount = ITEMCOUNT; //see RouterFactory.php
		$posts = $this->posts->findBy([], ['date' => 'DESC'], $paginator->itemsPerPage, $paginator->offset);
		$this->template->posts = $posts;
	}

	public function renderRss() {
		$this->template->posts = $this->posts->findBy([], ['date' => 'DESC'], 50);
	}

	public function renderSitemap() {
		$this->template->sitemap = $this->posts->findBy([]);
	}

}
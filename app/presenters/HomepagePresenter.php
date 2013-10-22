<?php

namespace App;

use Model;
use Nette;

class HomepagePresenter extends BasePresenter {

	/** @var \Model\Posts @inject */
	public $posts;

	public function renderDefault() {
		$vp = new \VisualPaginator($this, 'paginator');
		$paginator = $vp->getPaginator();
		$paginator->itemsPerPage = 8;
		$paginator->itemCount = ITEMCOUNT; //see RouterFactory.php

		$posts = $this->posts->getPosts($paginator->itemsPerPage, $paginator->offset);
		$this->template->posts = $posts;
	}

	public function renderRss() {
		$this->template->posts = $this->posts->getAllPosts()->order('date DESC')->limit(50);
	}

	public function renderSitemap() {
		$this->template->sitemap = $this->posts->getAllPosts();
	}

}

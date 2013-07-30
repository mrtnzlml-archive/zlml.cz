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
		$paginator->itemCount = count($this->posts->getAllPosts());

		//$paginator->lastPage TODO: přespěrovat pokud chce někdo přistoupit na fake paginator stránku

		$posts = $this->posts->getPosts($paginator->itemsPerPage, $paginator->offset);
		$this->template->posts = $posts;
	}

	public function renderRss() {
		$this->template->posts = $this->posts->getAllPosts()->order('date DESC')->limit(50);
	}

}

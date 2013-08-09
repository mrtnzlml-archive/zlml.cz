<?php

namespace App;

use Model;
use Nette;

class HomepagePresenter extends BasePresenter {

	/** @var \Model\Posts @inject */
	public $posts;
	/** @var \Model\Records @inject */
	public $records;

	public function renderDefault() {
		$vp = new \VisualPaginator($this, 'paginator');
		$paginator = $vp->getPaginator();
		$paginator->itemsPerPage = 8;
		$paginator->itemCount = count($this->posts->getAllPosts());

		$posts = $this->posts->getPosts($paginator->itemsPerPage, $paginator->offset);
		$this->template->posts = $posts;
	}

	public function renderRss() {
		$this->template->posts = $this->posts->getAllPosts()->order('date DESC')->limit(50);
	}

	public function renderSitemap() {
		$this->template->sitemap = $this->posts->getAllPosts();
	}

	///// POKUS /////

	public function handleInsertRecord($data = NULL) {
		if($data === NULL || empty($data)) {
			$this->sendPayload();
		} else {
			$data = serialize(array_filter(explode(",", $data)));
			$data = array(
				'data' => $data,
			);
			$this->records->newRecord($data);
			$this->sendPayload();
		}
	}

	public function handleGetRecords() {
		$records = $this->records->getRecords();
		$data = array();
		foreach($records as $record) {
			$data[] = unserialize($record->data);
		}
		$this->payload->pom = $data;
		$this->sendPayload();
	}

}

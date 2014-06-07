<?php

namespace App;

class SearchPresenter extends BasePresenter {

	/** @var Posts @inject */
	public $posts;
	/** @var Tags @inject */
	public $tags;

	public function renderDefault($search) {
		$this->template->tag = $this->tags->findOneBy(array('name' => $search));
		$result = $this->posts->fulltextSearch($search);
		if (count($result) == 0) {
			$this->template->search = $search;
			$this->template->error = 'Nic nebylo nalezeno';
		} else {
			$this->template->search = $search;
			$this->template->result = $result;
		}
	}

}
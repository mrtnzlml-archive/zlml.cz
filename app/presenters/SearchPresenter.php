<?php

namespace App;

class SearchPresenter extends BasePresenter {

	/** @var \Model\Posts @inject */
	public $posts;
	/** @var \Model\Tags @inject */
	public $tags;

	public function renderDefault($search) {
		//FIXME tagy ::: 'publish_date <=' => new \DateTime()
		$this->template->tag = $this->tags->findOneBy(['name' => $search]);
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

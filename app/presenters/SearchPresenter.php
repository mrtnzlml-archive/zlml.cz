<?php

namespace App;

class SearchPresenter extends BasePresenter {

	/** @var \Model\Posts @inject */
	public $posts;

	public function renderDefault($search = NULL) {

		if($search === NULL) {
			$this->template->error = 'Není co hledat!';
		} else {
			$this->template->search = $search;
			$this->template->result = $this->posts->fulltextSearch($search);
		}
	}

}
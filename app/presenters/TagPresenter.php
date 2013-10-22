<?php

namespace App;

class TagPresenter extends BasePresenter {

	/** @var \Model\Posts @inject */
	public $posts;

	//TODO: result nezná slug, takže na něj nelze odkázat
	public function renderDefault($search = NULL) {
		if ($search === NULL) {
			$this->template->error = 'Není co hledat!';
		} else {
			$tag = $this->posts->getTagByName($search)->fetch();
			$result = array();
			if ($tag) {
				$result = $this->posts->getPostsByTagID($tag->id);
			}
			if (count($result) == 0) {
				$this->template->search = $search;
				$this->template->error = 'Nic nebylo nalezeno';
			} else {
				$this->template->search = $search;
				$this->template->result = $result;
			}
		}
	}

}
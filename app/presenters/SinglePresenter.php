<?php

namespace App;

class SinglePresenter extends BasePresenter {

	/** @var \Model\Posts @inject */
	public $posts;

	public function renderObsah() {
		$articles = $this->posts->getAllPosts()->order('title ASC');
		$this->template->articles = $articles;
	}

	public function renderArticle($id) {
		$texy = new \fshlTexy();
		$texy->addHandler('block', array($texy, 'blockHandler'));
		$texy->tabWidth = 4;
		$texy->headingModule->top = 3; //start at H3

		$result = $this->posts->getPostByID($id);
		if ($result === FALSE) {
			$this->error();
		}
		$this->template->post = $result;
		$this->template->body = $texy->process($result->body);
		$this->template->url = $this->getHttpRequest()->getUrl();

		//TODO:
		// 1 - podle tagu
		// 2 - podle data
		// 3 - náhodný
		$next = array();
		$tags = iterator_to_array($this->posts->getTagsByPostID($id));
		foreach ($tags as $tag) {
			$posts = $this->posts->getPostsByTagID($tag->tag->id, $limit = 3);
			if (!empty($posts)) {
				foreach ($posts as $post) {
					if ($post->id == $id) {
						continue;
					} elseif (count($next) >= 3) {
						break;
					}
					$next[] = $post;
				}
			}
		}
		if (count($next) < 3) {
			foreach ($this->posts->getPosts(6 - count($next), 1) as $post) {
				if ($post->id == $id) {
					continue;
				} elseif (count($next) >= 3) {
					break;
				}
				$next[] = $post;
				$next = array_unique($next);
			}
		}
		$this->template->next = $next;
	}

	public function renderTags() {
		$this->template->tags = $this->posts->getAllTags();
	}

}
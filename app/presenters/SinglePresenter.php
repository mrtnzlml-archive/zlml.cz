<?php

namespace App;

class SinglePresenter extends BasePresenter {

	/** @var \Model\Posts @inject */
	public $posts;

	public function renderObsah() {
		$articles = $this->posts->getAllPosts()->order('title ASC');
		$this->template->articles = $articles;
	}

	public function renderArticle($slug) {
		// zobrazeni článku podle slugu
		$post = $this->posts->getBySlug($slug)->fetch();
		if (!$post) {
			$this->error();
		}

		$texy = new \fshlTexy();
		$texy->addHandler('block', array($texy, 'blockHandler'));
		$texy->tabWidth = 4;
		$texy->headingModule->top = 3; //start at H3

		$this->template->post = $post;
		$this->template->body = $texy->process($post->body);
		$this->template->url = $this->getHttpRequest()->getUrl();

		//TODO:
		// 1 - podle tagu
		// 2 - podle data
		// 3 - náhodný
		$next = array();
		$tags = iterator_to_array($this->posts->getTagsByPostID($post->id));
		foreach ($tags as $tag) {
			$articles = $this->posts->getPostsByTagID($tag->tag->id, $limit = 3);
			if (!empty($articles)) {
				foreach ($articles as $article) {
					if ($article->id == $post->id) {
						continue;
					} elseif (count($next) >= 3) {
						break;
					}
					$next[] = $article;
				}
			}
		}
		if (count($next) < 3) {
			foreach ($this->posts->getPosts(6 - count($next), 1) as $article) {
				if ($article->id == $post->id) {
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
<?php

namespace App;

use Nette;

class SinglePresenter extends BasePresenter {

	/** @var \Model\Tags @inject*/
	public $tags;

	public function renderObsah() {
		$articles = $this->posts->getAllPosts()->order('title ASC');
		$this->template->articles = $articles;
	}

	public function renderArticle($slug) {
		$old_slug = $slug;
		$slug = Nette\Utils\Strings::webalize($slug);
		if($old_slug !== $slug) {
			$this->redirect($slug);
		}
		$post = $this->posts->getBySlug($slug)->fetch(); // zobrazeni článku podle slugu
		if (!$post) { // pokud článek neexistuje (FALSE), pak forward - about, reference, atd...
			$this->forward($slug);
		} else { // zobrazení klasických článků
			$texy = new \fshlTexy();
			$texy->addHandler('block', array($texy, 'blockHandler'));
			$texy->tabWidth = 4;
			$texy->headingModule->top = 3; //start at H3

			$this->template->post = $post;
			$this->template->body = $texy->process($post->body);
			$this->template->url = $this->getHttpRequest()->getUrl();

			$prev = $this->posts->getAllPosts()->where('? > date', $post->date)->order('date DESC')->limit(1)->fetch();
			$next = $this->posts->getAllPosts()->where('? < date', $post->date)->order('date DESC')->limit(1)->fetch();
			$this->template->prevArticle = $prev;
			$this->template->nextArticle = $next;

			//TODO:
			// 1 - podle tagu
			// 2 - podle data (nejnovější)
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
					$next[] = $article;
					$next = array_unique($next);
				}
			}
			$this->template->next = $next;
		}
	}

}
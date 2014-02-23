<?php

namespace App;

use Nette;
use Nette\Http\Url;

class SinglePresenter extends BasePresenter {

	/** @var Tags @inject */
	public $tags;

	public function renderObsah() {
		$articles = $this->posts->findBy([], ['title' => 'ASC']);
		$this->template->articles = $articles;
	}

	public function renderTagsah() {
		$tags = $this->tags->findBy([], ['name' => 'ASC']);
		$this->template->tags = $tags;
	}

	public function renderArticle($slug) {
		$old_slug = $slug;
		$slug = Nette\Utils\Strings::webalize($slug);
		if ($old_slug !== $slug) {
			$this->redirect($slug);
		}
		$post = $this->posts->findOneBy(['slug' => $slug]); // zobrazeni článku podle slugu
		if (!$post) { // pokud článek neexistuje (FALSE), pak forward - about, reference, atd...
			$this->forward($slug);
		} else { // zobrazení klasických článků
			$texy = new \fshlTexy();
			$texy->addHandler('phrase', function ($invocation, $phrase, $content, $modifier, $link) {
				$el = $invocation->proceed();
				if ($el instanceof \TexyHtml && $el->getName() === 'a') {
					$url = new Url($el->attrs['href']);
					$httpRequest = $this->presenter->getHttpRequest();
					$uri = $httpRequest->getUrl();
					if ($url->authority != $uri->host) {
						$el->attrs['target'] = '_blank';
					}
				}
				return $el;
			});
			$texy->addHandler('block', array($texy, 'blockHandler'));
			$texy->tabWidth = 4;
			$texy->headingModule->top = 3; //start at H3
			$texy->headingModule->generateID = TRUE;

			$this->template->post = $post;
			$this->template->body = $texy->process($post->body);
			$this->template->url = $this->getHttpRequest()->getUrl();

			$prev = $this->posts->findOlder($post->date);
			$next = $this->posts->findNewer($post->date);
			$this->template->prevArticle = $prev;
			$this->template->nextArticle = $next;

			$ids = $next = array();
			if (isset($post->tags[0])) {
				$next = $this->posts->findBy(['id !=' => $post->getId(), 'tags.id' => $post->tags], ['date' => 'DESC'], 3);
				foreach ($next as $n) {
					array_push($ids, $n->id);
				}
			}
			if (count($next) < 3) {
				$limit = 3 - count($next);
				if($ids) {
					$next = array_merge($next, $this->posts->findBy(['id !=' => $post->getId(), 'id != ' => $ids], ['date' => 'DESC'], $limit));
				} else {
					$next = array_merge($next, $this->posts->findBy(['id !=' => $post->getId()], ['date' => 'DESC'], $limit));
				}
			}
			$this->template->next = $next;
		}
	}

}
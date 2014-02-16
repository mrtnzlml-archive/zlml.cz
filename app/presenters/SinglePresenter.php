<?php

namespace App;

use Nette;

class SinglePresenter extends BasePresenter {

	/** @var Tags @inject */
	public $tags;

	public function renderObsah() {
		$articles = $this->posts->findBy(array(), ['title' => 'ASC']);
		$this->template->articles = $articles;
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
			$texy->addHandler('block', array($texy, 'blockHandler'));
			$texy->tabWidth = 4;
			$texy->headingModule->top = 3; //start at H3

			$this->template->post = $post;
			$this->template->body = $texy->process($post->body);
			$this->template->url = $this->getHttpRequest()->getUrl();

			$prev = $this->posts->findOlder($post->date);
			$next = $this->posts->findNewer($post->date);
			$this->template->prevArticle = $prev;
			$this->template->nextArticle = $next;

			//TODO:
			$next = $this->posts->findBy(['id !=' => $post->getId(), 'tags.name' => 'pcre'], ['date' => 'DESC']);
			if (count($next) < 3) {
				$limit = 3 - count($next);
				//nesmí se tahat (pole) IDs co už tam jsou...
				array_push($next, $next[1] = $this->posts->findOneBy(['id !=' => $post->getId()], ['date' => 'DESC'], $limit));
			}
			$this->template->next = $next;
		}
	}

}
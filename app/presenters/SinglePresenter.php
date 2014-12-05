<?php

namespace App;

use Model;
use Nette;
use Nette\Http\Url;

class SinglePresenter extends BasePresenter {

	/** @var \ISignInFormFactory @inject */
	public $signInFormFactory;
	/** @var \Model\Tags @inject */
	public $tags;

	public function renderObsah() {
		if (!$this->setting->show_content) {
			$this->error();
		}
		$this->template->articles = $this->posts->findBy(['publish_date <=' => new \DateTime()], ['title' => 'ASC']);
	}

	public function renderArticle($slug) {
		$webalized = Nette\Utils\Strings::webalize($slug);
		if (empty($webalized)) {
			$this->redirect('Homepage:default');
		}
		if ($slug !== $webalized) {
			$this->redirect('Single:article', $webalized);
		}
		$post = $this->posts->findOneBy(['slug' => $webalized, 'publish_date <=' => new \DateTime()]); // zobrazení článku podle slugu
		$page = $this->pages->findOneBy(['slug' => $webalized]); // zobrazení stránky podle slugu
		if (!$post && !$page) { // pokud článek neexistuje (FALSE), pak forward - about, reference, atd...
			$this->forward($webalized);
		} elseif ($post) { // zobrazení klasických článků
			$texy = $this->prepareTexy();
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

			$this->template->post = $post;
			$body = $texy->process($post->body);
			$this->template->body = $body;
			$this->template->wordCount = str_word_count(strip_tags($body));

			$prev = $this->posts->findOlder($post->date);
			$next = $this->posts->findNewer($post->date);
			$this->template->prevArticle = $prev;
			$this->template->nextArticle = $next;

			$ids = $next = [];
			if (isset($post->tags[0])) {
				$next = $this->posts->findBy(['id !=' => $post->getId(), 'tags.id' => $post->tags], ['date' => 'DESC'], 3);
				foreach ($next as $n) {
					array_push($ids, $n->id);
				}
			}
			if (count($next) < 3) {
				$limit = 3 - count($next);
				if ($ids) {
					$next = array_merge((array)$next, (array)$this->posts->findBy(['id !=' => $post->getId(), 'id != ' => $ids], ['date' => 'DESC'], $limit));
				} else {
					$next = array_merge((array)$next, (array)$this->posts->findBy(['id !=' => $post->getId()], ['date' => 'DESC'], $limit));
				}
			}
			$this->template->next = $next;
		} else { //PAGE
			$this->setView('page');
			$texy = $this->prepareTexy();
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
			$this->template->page = $page;
			$body = $texy->process($page->body);
			$this->template->body = $body;
		}
	}

	/** @return \Cntrl\SignIn */
	protected function createComponentSignInForm() {
		return $this->signInFormFactory->create();
	}

}

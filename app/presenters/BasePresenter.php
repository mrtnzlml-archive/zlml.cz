<?php

namespace App;

use Latte;
use Nette;
use Nette\Application\UI;
use WebLoader;

abstract class BasePresenter extends Nette\Application\UI\Presenter {

	/** @var \Model\Posts @inject */
	public $posts;
	/** @var \Nette\Http\Session @inject */
	public $session;
	/** @var \Model\Settings @inject */
	public $settings;
	/** @var \Model\Pages @inject */
	public $pages;
	/** @var \WebLoader\LoaderFactory @inject */
	public $webLoader;

	protected $setting;

	public function startup() {
		parent::startup();
		$this->template->setting = $this->setting = $this->settings->findAllByKeys();
		$this->template->pages = $this->pages->findBy([]);
	}

	protected function createComponentSearch() {
		$form = new UI\Form;
		$form->addText('search')
			->setRequired('Vyplňte co chcete vyhledávat.')
			->setValue($this->getParameter('search'));
		$form->addSubmit('send', 'Go!');
		$form->onSuccess[] = $this->searchSucceeded;
		return $form;
	}

	public function searchSucceeded(UI\Form $form, $values) {
		$this->redirect('Search:default', $values->search);
	}

	/**
	 * @param null $class
	 * @return Nette\Templating\ITemplate
	 */
	protected function createTemplate($class = NULL) {
		$template = parent::createTemplate($class);
		$template->registerHelper('texy', function ($input) {
			$texy = new \Texy();
			$html = new Nette\Utils\Html();
			return $html::el()->setHtml($texy->process($input));
		});
		$template->registerHelper('vlna', function ($string) {
			$string = preg_replace('<([^a-zA-Z0-9])([ksvzaiou])\s([a-zA-Z0-9]{1,})>i', "$1$2\xc2\xa0$3", $string); //&nbsp; === \xc2\xa0
			return $string;
		});
		$template->registerHelper('dateInWords', function ($time) {
			$time = Nette\Utils\DateTime::from($time);
			$months = [1 => 'leden', 'únor', 'březen', 'duben', 'květen', 'červen', 'červenec', 'srpen', 'září', 'říjen', 'listopad', 'prosinec'];
			return $time->format('j. ') . $months[$time->format('n')] . $time->format(' Y');
		});
		$template->registerHelper('timeAgoInWords', function ($time) {
			$time = Nette\Utils\DateTime::from($time);
			$delta = round((time() - $time->getTimestamp()) / 60);
			if ($delta == 0) return 'před okamžikem';
			if ($delta == 1) return 'před minutou';
			if ($delta < 45) return "před $delta minutami";
			if ($delta < 90) return 'před hodinou';
			if ($delta < 1440) return 'před ' . round($delta / 60) . ' hodinami';
			if ($delta < 2880) return 'včera';
			if ($delta < 43200) return 'před ' . round($delta / 1440) . ' dny';
			if ($delta < 86400) return 'před měsícem';
			if ($delta < 525960) return 'před ' . round($delta / 43200) . ' měsíci';
			if ($delta < 1051920) return 'před rokem';
			return 'před ' . round($delta / 525960) . ' lety';
		});
		return $template;
	}

	public function createComponentCss() {
		return $this->webLoader->createCssLoader('default')->setMedia('screen,projection,tv,print');
	}

	public function createComponentJs() {
		return $this->webLoader->createJavaScriptLoader('default');
	}

	public function handleRandom() {
		if (!$this->setting->random_search) {
			$this->error();
		}
		$post = $this->posts->rand();
		if ($post) {
			$this->redirect(':Single:article', $post->slug);
		}
		$this->redirect(':Homepage:default');
	}

	/**
	 * @return \fshlTexy
	 */
	protected function prepareTexy() {
		$texy = new \fshlTexy();
		$texy->addHandler('block', [$texy, 'blockHandler']);
		$texy->tabWidth = 4;
		$texy->headingModule->top = 3; //start at H3
		$texy->headingModule->generateID = TRUE;
		$texy->imageModule->root = $this->getHttpRequest()->getUrl()->getBaseUrl() . 'uploads/';
		$texy->imageModule->leftClass = 'leftAlignedImage';
		$texy->imageModule->rightClass = 'rightAlignedImage';
		return $texy;
	}

}

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
		$files = new WebLoader\FileCollection(WWW_DIR . '/css');
		$files->addFiles([
			'bootstrap.css',
			'screen.less',
		]);
		$compiler = WebLoader\Compiler::createCssCompiler($files, WWW_DIR . '/webtemp');
		$compiler->setOutputNamingConvention(\OutputNamingConvention::createCssConvention());
		$compiler->addFileFilter(new WebLoader\Filter\LessFilter());
		$compiler->addFilter(function ($code) {
			$code = Latte\Runtime\Filters::strip($code);
			$code = Latte\Runtime\Filters::trim($code);
			$code = preg_replace('/\/\*(.*?)\*\//s', '', $code); //remove comments
			return $code;
		});
		return new WebLoader\Nette\CssLoader($compiler, $this->template->basePath . '/webtemp');
	}

	public function createComponentJs() {
		$files = new WebLoader\FileCollection(WWW_DIR . '/js');
		$files->addFiles([
			'jquery.js',
			'bootstrap.js',
			'jquery.qrcode-0.6.0.js',
			'netteForms.js',
			'nette.ajax.js',
			'history.ajax.js',
			'main.js',
		]);
		$compiler = WebLoader\Compiler::createJsCompiler($files, WWW_DIR . '/webtemp');
		$compiler->setOutputNamingConvention(\OutputNamingConvention::createJsConvention());
		$compiler->addFilter(function ($code) {
			$code = preg_replace('/\/\*(.*?)\*\//s', '', $code); //remove multiline comments - inline warning!
			$code = preg_replace('/(^[\r\n]*|[\r\n]+)[\s\t]*[\r\n]+/', "\n", $code); //remove new lines
			$code = preg_replace('/(^[ \t]+)(.+)$/m', "$2", $code); //odstraní úvodní mezery a tabulátory na řádku
			return trim($code);
		});
		return new \Zeminem\JavaScriptLoader($compiler, $this->template->basePath . '/webtemp');
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

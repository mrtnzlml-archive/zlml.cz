<?php

namespace App;

use Model;
use Nette;

abstract class BasePresenter extends Nette\Application\UI\Presenter {

	/** @persistent */
	//public $lang = 'cs';
	/** @var \GettextTranslator\Gettext @inject */
	//public $translator;

	/** @var \Model\Posts @inject */
	public $posts;

	public function beforeRender() {
		parent::beforeRender();
		if ($this->isAjax()) {
			$this->invalidateControl('title');
			$this->invalidateControl('content');
		}
	}

	protected function createComponentSearch() {
		$form = new \Nette\Application\UI\Form;
		$form->addText('search')
			->setRequired('Vyplňte co chcete vyhledávat.')
			->setValue($this->getParameter('search'));
		$form->addSubmit('send', 'Go!');
		$form->onSuccess[] = $this->searchSucceeded;
		return $form;
	}

	public function searchSucceeded($form) {
		$vals = $form->getValues();
		$this->redirect('Search:default', $vals->search);
	}

	/**
	 * @param null $class
	 * @return Nette\Templating\ITemplate
	 */
	protected function createTemplate($class = NULL) {
		$template = parent::createTemplate($class);
		$texy = new \Texy();
		$template->registerHelper('texy', callback($texy, 'process'));
		$template->registerHelper('vlna', function ($string) {
			$string = preg_replace('<([^a-zA-Z0-9])([vszouai])\s([a-zA-Z0-9]{1,})>', "$1$2\xc2\xa0$3", $string); //&nbsp; === \xc2\xa0
			return $string;
		});

		// if not set, the default language will be used
		/*if (!isset($this->lang)) {
			$this->lang = $this->translator->getLang();
		} else {
			$this->translator->setLang($this->lang);
		}
		$template->setTranslator($this->translator);*/

		return $template;
	}

	public function createComponentCss() {
		$files = new \WebLoader\FileCollection(WWW_DIR . '/css');
		$files->addFiles(array(
			'bootstrap.css',
			'screen.less',
		));
		$compiler = \WebLoader\Compiler::createCssCompiler($files, WWW_DIR . '/webtemp');
		$compiler->setOutputNamingConvention(\ZeminemOutputNamingConvention::createCssConvention());
		$compiler->addFileFilter(new \Webloader\Filter\LessFilter());
		$compiler->addFilter(function ($code) {
			return \CssMin::minify($code);
		});
		return new \WebLoader\Nette\CssLoader($compiler, $this->template->basePath . '/webtemp');
	}

	public function createComponentJs() {
		$files = new \WebLoader\FileCollection(WWW_DIR . '/js');
		$files->addFiles(array(
			'jquery.js',
			'bootstrap.js',
			'jquery.qrcode-0.6.0.js',
			'jquery.fracs-0.11.js',
			'jquery.outline-0.11.js',
			'netteForms.js',
			'nette.ajax.js',
			'history.ajax.js',
			'main.js',
		));
		$compiler = \WebLoader\Compiler::createJsCompiler($files, WWW_DIR . '/webtemp');
		$compiler->setOutputNamingConvention(\ZeminemOutputNamingConvention::createJsConvention());
		$compiler->addFilter(function ($code) {
			return \JSMin::minify($code);
		});
		return new \WebLoader\Nette\JavaScriptLoader($compiler, $this->template->basePath . '/webtemp');
	}

	public function handleRandom() {
		$post = $this->posts->getAllPosts()->order('RAND()')->limit(1)->fetch();
		$this->redirect('Single:article', $post->slug);
	}

}
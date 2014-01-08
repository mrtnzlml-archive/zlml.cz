<?php

namespace App;

use Model;
use Nette;
use WebLoader;

abstract class BasePresenter extends Nette\Application\UI\Presenter {

	/** @var \Model\Posts @inject */
	public $posts;
	/** @var \Nette\Http\Session @inject */
	public $session;

	public function beforeRender() {
		parent::beforeRender();
		if ($this->isAjax()) {
			$this->redrawControl('title');
			$this->redrawControl('menu');
			$this->redrawControl('flashes');
			$this->redrawControl('content');
		}

		$section = $this->session->getSection('experimental');
		if ($section->experimental == NULL) {
			$section->experimental = 'none';
			$section->experimental_data = array();
		}
		$this->template->experimental = $section->experimental;
		$this->template->experimental_data = json_encode($section->experimental_data);
	}

	protected function createComponentSearch() {
		$form = new Nette\Application\UI\Form;
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
		return $template;
	}

	public function createComponentCss() {
		$files = new WebLoader\FileCollection(WWW_DIR . '/css');
		$files->addFiles(array(
			'bootstrap.css',
			'screen.less',
		));
		$compiler = WebLoader\Compiler::createCssCompiler($files, WWW_DIR . '/webtemp');
		$compiler->setOutputNamingConvention(\ZeminemOutputNamingConvention::createCssConvention());
		$compiler->addFileFilter(new Webloader\Filter\LessFilter());
		$compiler->addFilter(function ($code) {
			return \CssMin::minify($code);
		});
		return new WebLoader\Nette\CssLoader($compiler, $this->template->basePath . '/webtemp');
	}

	public function createComponentJs() {
		$files = new WebLoader\FileCollection(WWW_DIR . '/js');
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
		$compiler = WebLoader\Compiler::createJsCompiler($files, WWW_DIR . '/webtemp');
		$compiler->setOutputNamingConvention(\ZeminemOutputNamingConvention::createJsConvention());
		$compiler->addFilter(function ($code) {
			return \JSMin::minify($code);
		});
		return new \Zeminem\JavaScriptLoader($compiler, $this->template->basePath . '/webtemp');
	}

	public function handleRandom() {
		$post = $this->posts->getAllPosts()->order('RAND()')->limit(1)->fetch();
		$this->redirect('Single:article', $post->slug);
	}

	public function handleExperimental() {
		$section = $this->session->getSection('experimental');
		if ($section->experimental == 'none') {
			$section->experimental = 'all';
			$this->flashMessage('Experimentální funkce zapnuty.', 'alert-info');
		} else {
			$section->experimental = 'none';
			$this->flashMessage('Experimentální funkce vypnuty.', 'alert-info');
		}
		$this->redirect('this');
	}

	// TODO
	public function handleExperimentalData($data = NULL) {
		$section = $this->session->getSection('experimental');
		if ($data !== NULL) {
			$oldData = $section->experimental_data;
		}
		$this->redirect('this');
	}

}
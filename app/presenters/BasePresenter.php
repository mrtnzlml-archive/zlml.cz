<?php

namespace App;

use Model;
use Nette;

abstract class BasePresenter extends Nette\Application\UI\Presenter {

	//SELECT *
	//FROM mirror_posts
	//WHERE MATCH(title, body) AGAINST ('$search' IN BOOLEAN MODE)
	//ORDER BY 5 * MATCH(title) AGAINST ('$search') + MATCH(body) AGAINST ('$search') DESC

	protected function createComponentSearch() {
		$form = new \Nette\Application\UI\Form;
		$form->addText('search')
			->setRequired('Vyplňte co chcete vyhledávat.');
		$form->addSubmit('send', '');
		$form->onSuccess[] = $this->searchSucceeded;
		return $form;
	}

	public function searchSucceeded($form) {
		$vals = $form->getValues();
		$this->redirect('Search:default', $vals->search);
	}

	protected function createTemplate($class = NULL) {
		$template = parent::createTemplate($class);
		$texy = new \Texy();
		/*$texy->addHandler('phrase', function($invocation, $phrase, $content, $modifier, $link) {
			$el = $invocation->proceed();
			if ($el instanceof \TexyHtml && $el->getName() === 'a') {
				$el->attrs['target'] = '_blank';
			}
			return $el;
		});*/
		$template->registerHelper('texy', callback($texy, 'process'));
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
			'main.js',
		));
		$compiler = \WebLoader\Compiler::createJsCompiler($files, WWW_DIR . '/webtemp');
		$compiler->setOutputNamingConvention(\ZeminemOutputNamingConvention::createJsConvention());
		$compiler->addFilter(function ($code) {
			return \JSMin::minify($code);
		});
		return new \WebLoader\Nette\JavaScriptLoader($compiler, $this->template->basePath . '/webtemp');
	}

}
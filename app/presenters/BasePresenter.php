<?php

namespace App;

use Model;
use Nette;

abstract class BasePresenter extends Nette\Application\UI\Presenter {

	public function beforeRender() {
		$content = md5('http://www.zeminem.cz' . $this->link('this'));
		$back_color = 0xFFFFFF;
		$fore_color = 0x000000;
		if (!is_file(__DIR__  . '/../../www/qrcache/' . $content . '.png')) { //QR se generuje pro každou URL jen jednou
			\QRcode::png('' . $this->getHttpRequest()->getUrl(), __DIR__ . '/../../www/qrcache/' . $content . '.png', 'M', 4, 1, false, $back_color, $fore_color);
		}
		$this->template->qr = '<img src="' . $this->context->httpRequest->url->baseUrl . '/../qrcache/' . $content . '.png" width="100" height="100" style="margin:5px 0;" alt="QR Code">';
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
			//'jquery.qrcode-0.6.0.js',
			'jquery.fracs-0.11.js',
			'jquery.outline-0.11.js',
			'netteForms.js',
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
<?php declare(strict_types=1);

namespace App;

use Nette;
use Tracy;


/**
 * Error presenter.
 */
class ErrorPresenter extends BasePresenter
{

	/**
	 * @param  Exception
	 *
	 * @return void
	 */
	public function renderDefault($exception)
	{
		if ($this->isAjax()) { // AJAX request? Just note this error in payload.
			$this->payload->error = TRUE;
			$this->terminate();

		} elseif ($exception instanceof Nette\Application\BadRequestException) {
			$code = $exception->getCode();
			// load template 403.latte or 404.latte or ... 4xx.latte
			$this->setView(in_array($code, [403, 404, 405, 410, 500]) ? $code : '4xx');
			$request = $this->parameters['request'];
			if ($request) {
				$this->template->test = $request->parameters['action'];
			}
			// log to access.log
			Tracy\Debugger::log("HTTP code $code: {$exception->getMessage()} in {$exception->getFile()}:{$exception->getLine()}", 'access');
		} else {
			$this->setView('500'); // load template 500.latte
			Tracy\Debugger::log($exception, Tracy\Debugger::ERROR); // and log exception
		}
	}

}

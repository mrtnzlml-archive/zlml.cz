<?php

namespace Nette\Application\Responses;

use Nette;

class NotImplementedResponse extends Nette\Object implements Nette\Application\IResponse
{

	private $contentType = 'application/json';

	public function send(Nette\Http\IRequest $httpRequest, Nette\Http\IResponse $httpResponse)
	{
		$httpResponse->setContentType($this->contentType);
		$httpResponse->setExpiration(FALSE);
		echo Nette\Utils\Json::encode([
			'method' => 'POST',
			'err_code' => 500,
			'err_info' => 'This HTTP method is not supported yet.',
		]);
	}

}

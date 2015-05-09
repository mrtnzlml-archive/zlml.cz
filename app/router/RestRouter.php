<?php

namespace App;

// http://forum.nette.org/cs/10824-podpora-rest-sluzeb-v-nette-2-1
// https://github.com/newPOPE/Nette-RestRoute/blob/master/src/RestRoute.php
// http://forum.nette.org/cs/4617-http-verbs-restful-architektura
// TODO: https://dev.twitter.com/docs/rate-limiting/1.1

use Nette;
use Nette\Http\IRequest;

class RestRouter extends Nette\Application\Routers\Route
{

	const METHOD_POST = 4;
	const METHOD_GET = 8;
	const METHOD_PUT = 16;
	const METHOD_DELETE = 32;
	const RESTFUL = 64;

	public function match(IRequest $httpRequest)
	{
		$httpMethod = $httpRequest->getMethod();
		if (($this->flags & self::RESTFUL) == self::RESTFUL) {
			$presenterRequest = parent::match($httpRequest);
			if ($presenterRequest != NULL) {
				switch ($httpMethod) {
					//see: http://en.wikipedia.org/wiki/Representational_state_transfer#RESTful_web_APIs
					case 'GET':
						$action = 'get';
						break;
					case 'POST':
						$action = 'post';
						break; //CREATE
					case 'PUT':
						$action = 'put';
						break; //UPDATE
					case 'DELETE':
						$action = 'delete';
						break;
					default:
						$action = 'get';
				}
				$params = $presenterRequest->getParameters();
				$params['action'] = $action;
				$presenterRequest->setParameters($params);
				return $presenterRequest;
			} else {
				return NULL;
			}
		}

		if (($this->flags & self::METHOD_POST) == self::METHOD_POST && $httpMethod != 'POST') {
			return NULL;
		}
		if (($this->flags & self::METHOD_GET) == self::METHOD_GET && $httpMethod != 'GET') {
			return NULL;
		}
		if (($this->flags & self::METHOD_PUT) == self::METHOD_PUT && $httpMethod != 'PUT') {
			return NULL;
		}
		if (($this->flags & self::METHOD_DELETE) == self::METHOD_DELETE && $httpMethod != 'DELETE') {
			return NULL;
		}

		return parent::match($httpRequest);
	}

}

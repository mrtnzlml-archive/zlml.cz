<?php

namespace App\RestModule;

use Model;
use Nette\Application\Responses\JsonResponse;
use Nette\Application\UI;

class ArticlePresenter extends UI\Presenter {

	/** @var Model\Posts @inject */
	public $posts;

	// http://www.thebuzzmedia.com/designing-a-secure-rest-api-without-oauth-authentication/

	/*
	 * [CLIENT]		- send public API key (user-identifiable information)
	 * [CLIENT]		- generate and send HMAC hash (using private key) - URL! - see http://www.thebuzzmedia.com/designing-a-secure-rest-api-without-oauth-authentication/comment-page-1/#comment-572339
	 * [SERVER]		- get client private key based on public key (from DB)
	 * [SERVER]		- generate HMAC hash (using private key)
	 * [SERVER]		- compare both hashes and return response (or failure)
	 */

	public function actionGet($id = NULL) {
		$api_key = $this->getHttpRequest()->getHeader('public-api-key');
		$api_hash = $this->getHttpRequest()->getHeader('api-hash');

		date_default_timezone_set('UTC'); //TODO: -1min
		$date = date('YmdHi');
		$actual_url = $this->getHttpRequest()->getUrl()->getAbsoluteUrl();
		$hmac = hash_hmac('sha512', $actual_url . $date, 'PRIVATE_KEY'); //na základě public key ($api_key)

		$payload = array(
			'api_hash' => $this->getHttpRequest()->getHeader('api-hash'),
		);

		$response['method'] = 'GET';
		if ($api_hash !== $hmac) {
			$response['err_code'] = 10;
			$response['err_info'] = 'You are not authorized.';
			$this->sendResponse(new JsonResponse($response));
		} elseif ($id === NULL) {
			$response['err_code'] = 20;
			$response['err_info'] = 'Requested ID cannot be empty.';
			$this->sendResponse(new JsonResponse($response));
		} else {
			$response['data'] = $this->posts->findForApi(['id' => $id]); //FIXME: vracet 1 result, ne $result[0]
			$this->sendResponse(new JsonResponse($response));
		}
	}

	public function actionPost() {
		//http://stackoverflow.com/questions/8945879/how-to-get-body-of-a-post-in-php
		//$request_body = file_get_contents('php://input');
		$this->sendResponse(new JsonResponse(array(
			'method' => 'POST',
			'err_code' => 500,
			'err_info' => 'This HTTP method is not supported yet.',
		)));
	}

	public function actionPut() {
		$this->sendResponse(new JsonResponse(array(
			'method' => 'PUT',
			'err_code' => 500,
			'err_info' => 'This HTTP method is not supported yet.',
		)));
	}

	public function actionDelete() {
		$this->sendResponse(new JsonResponse(array(
			'method' => 'DELETE',
			'err_code' => 500,
			'err_info' => 'This HTTP method is not supported yet.',
		)));
	}

}

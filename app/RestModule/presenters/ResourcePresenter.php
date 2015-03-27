<?php

namespace App\RestModule;

use Nette\Application\Responses\JsonResponse;
use Nette\Application\UI;

class ResourcePresenter extends UI\Presenter {

	// http://www.thebuzzmedia.com/designing-a-secure-rest-api-without-oauth-authentication/

	/*
	 * [CLIENT]		- send public API key (user-identifiable information)
	 * [CLIENT]		- generate and send HMAC hash (using private key) - URL! - see http://www.thebuzzmedia.com/designing-a-secure-rest-api-without-oauth-authentication/comment-page-1/#comment-572339
	 * [SERVER]		- get client private key based on public key (from DB)
	 * [SERVER]		- generate HMAC hash (using private key)
	 * [SERVER]		- compare both hashes and return response (or failure)
	 */

	public function actionGet() {
		$api_key = $this->getHttpRequest()->getHeader('public-api-key');
		$api_hash = $this->getHttpRequest()->getHeader('api-hash');

		date_default_timezone_set('UTC'); //TODO: -1min
		$date = date('YmdHi');
		$actual_url = $this->getHttpRequest()->getUrl()->getAbsoluteUrl();
		$hmac = hash_hmac('sha512', $actual_url . $date, 'PRIVATE_KEY'); //na základě public key ($api_key)

		$response['method'] = 'GET';
		if ($api_hash !== $hmac) {
			$response['err_code'] = 10;
			$response['err_info'] = 'You are not authorized.';
			$this->sendResponse(new JsonResponse($response));
		} else {
			$response['data'] = array(
				'info' => 'Everything works fine.'
			);
			$this->sendResponse(new JsonResponse($response));
		}
	}

}

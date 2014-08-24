<?php

namespace Nette\Application\Responses;

use Nette;

/**
 * JSON response used for API requests.
 *
 * @property-read array|\stdClass $payload
 * @property-read string $contentType
 */
class ApiResponse extends Nette\Object implements Nette\Application\IResponse {

	/** @var array|\stdClass */
	private $payload;

	/** @var string */
	private $contentType;

	private $errors = array(
		10 => 'You are not authorized.',
		20 => 'Requested ID cannot be empty.',
		500 => 'This HTTP method is not supported yet.',
	);

	/**
	 * @param $payload array|\stdClass payload
	 * @param string $contentType MIME content type
	 * @throws Nette\InvalidArgumentException
	 */
	public function __construct($payload, $contentType = NULL) {
		if (!is_array($payload) && !is_object($payload)) {
			throw new Nette\InvalidArgumentException(sprintf('Payload must be array or object class, %s given.', gettype($payload)));
		}
		$this->payload = $payload;
		$this->contentType = $contentType ? $contentType : 'application/json';
	}

	/**
	 * @return array|\stdClass
	 */
	public function getPayload() {
		return $this->payload;
	}

	/**
	 * Returns the MIME content type of a downloaded file.
	 * @return string
	 */
	public function getContentType() {
		return $this->contentType;
	}

	/**
	 * Sends response to output.
	 * @param Nette\Http\IRequest $httpRequest
	 * @param Nette\Http\IResponse $httpResponse
	 * @return void
	 */
	public function send(Nette\Http\IRequest $httpRequest, Nette\Http\IResponse $httpResponse) {
		$httpResponse->setContentType($this->contentType);
		$httpResponse->setExpiration(FALSE);

		$response = array();
		$actual_url = $httpRequest->getUrl()->getAbsoluteUrl();
		date_default_timezone_set('UTC'); //TODO: -1min
		$date = date('YmdHi');
		$hmac = hash_hmac('sha512', $actual_url . $date, 'PRIVATE_KEY'); //TODO: na základě public key ($api_key)
		if ($this->payload['api_hash'] !== $hmac) {
			$response['err_code'] = 10;
			$response['err_info'] = $this->errors[10];
		}

		echo Nette\Utils\Json::encode($this->payload);
	}

}

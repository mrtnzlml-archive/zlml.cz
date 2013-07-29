<?php

namespace Model;

use Nette\Utils\Json;

/**
 * Minimalistic Google OAuth2 connector
 * @author Mikuláš Dítě
 */
class Google extends \Nette\Object {

	const URL_AUTH = 'https://accounts.google.com/o/oauth2/auth';
	const URL_TOKEN = 'https://accounts.google.com/o/oauth2/token';
	const URL_INFO = 'https://www.googleapis.com/oauth2/v1/userinfo';

	/** @var string client_id */
	private $id;

	/** @var string client_secret */
	private $secret;

	public function __construct(array $config) {
		$this->id = $config['id'];
		$this->secret = $config['secret'];
	}

	public function getLoginUrl(array $args) {
		$query = [
			'response_type' => 'code',
			'client_id' => $this->id,
			'redirect_uri' => $args['redirect_uri'],
			'access_type' => 'online',
			'scope' => implode(' ', $args['scope']),
			//'approval_prompt' => 'force',
		];

		if (isset($args['state'])) {
			$query['state'] = $args['state'];
		}

		return self::URL_AUTH . '?' . http_build_query($query);
	}


	public function getToken($code, $uri) {
		$query = [
			'code' => $code,
			'redirect_uri' => $uri,
			'client_id' => $this->id,
			'client_secret' => $this->secret,
			'grant_type' => 'authorization_code',
		];

		$content = http_build_query($query);
		$c = curl_init();
		curl_setopt_array($c, [
			CURLOPT_URL => self::URL_TOKEN,
			CURLOPT_POST => TRUE,
			CURLOPT_POSTFIELDS => $content,
			CURLOPT_RETURNTRANSFER => TRUE,
			CURLOPT_SSL_VERIFYHOST => FALSE,
			CURLOPT_SSL_VERIFYPEER => FALSE,
			CURLOPT_HTTPHEADER => [
				'Content-type: application/x-www-form-urlencoded',
				'Content-length: ' . strlen($content)
			],
		]);
		$res = curl_exec($c);
		curl_close($c);
		$data = Json::decode($res);

		if (isset($data->error)) {
			throw new GoogleException("Error while obtaining token from code: {$data->error}");
		}

		return $data->access_token;
	}


	public function getInfo($token) {
		$res = file_get_contents(self::URL_INFO . '?' . http_build_query([
			'access_token' => $token,
		]));

		return Json::decode($res);
	}

}

class GoogleException extends \RuntimeException {
}
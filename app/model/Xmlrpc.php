<?php

namespace Model;

use Nette;
use Nette\Utils\Validators;

/**
 * Class Xmlrpc
 * @package Model
 */
class Xmlrpc extends Nette\Object {

	const REGEXP_PINGBACK_LINK = '<link rel="pingback" href="([^"]+)" ?/?>';
	const RESPONSE_SUCCESS = -1;
	//const RESPONSE_FAULT_GENERIC = 0;
	const RESPONSE_FAULT_SOURCE = 0x0010;
	const RESPONSE_FAULT_SOURCE_LINK = 0x0011;
	const RESPONSE_FAULT_TARGET = 0x0020;
	const RESPONSE_FAULT_TARGET_INVALID = 0x0021;
	//const RESPONSE_FAULT_ALREADY_REGISTERED = 0x0030;
	//const RESPONSE_FAULT_ACCESS_DENIED = 0x0031;

	private $responses = [
		self::RESPONSE_SUCCESS => 'Success',
		//self::RESPONSE_FAULT_GENERIC => 'Unknown error.',
		self::RESPONSE_FAULT_SOURCE => 'The source URI does not exist.',
		self::RESPONSE_FAULT_SOURCE_LINK => 'The source URI does not contain a link to the target URI, and so cannot be used as a source.',
		self::RESPONSE_FAULT_TARGET => 'The specified target URI does not exist.',
		self::RESPONSE_FAULT_TARGET_INVALID => 'The specified target URI cannot be used as a target.',
		//self::RESPONSE_FAULT_ALREADY_REGISTERED => 'The pingback has already been registered.',
		//self::RESPONSE_FAULT_ACCESS_DENIED => 'Access denied.'
	];

	private $server;
	private $pingback_url;

	//Kdyby\Curl ?

	public function __construct() {
		$this->server = xmlrpc_server_create();
		//http://www.hixie.ch/specs/pingback/pingback
		//https://github.com/tedeh/pingback-php
		xmlrpc_server_register_method($this->server, 'pingback.ping', [$this, 'pingback_ping']);
	}

	public function __destruct() {
		xmlrpc_server_destroy($this->server);
	}

	public function pingback_ping($source, $target) {
		if (!Validators::isUrl($source)) {
			return $this->xmlrpc_fault(self::RESPONSE_FAULT_SOURCE);
		}

		if (!Validators::isUrl($target)) {
			return $this->xmlrpc_fault(self::RESPONSE_FAULT_TARGET);
		}

		$curl = curl_init($target);
		curl_setopt($curl, CURLOPT_HEADER, true);
		curl_setopt($curl, CURLOPT_NOBODY, true);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		$headers = http_parse_headers(curl_exec($curl));
		curl_close($curl);
		if (isset($headers['X-Pingback']) && !empty($headers['X-Pingback'])) {
			$this->pingback_url = $headers['X-Pingback'];
		} else {
			if (preg_match(self::REGEXP_PINGBACK_LINK, file_get_contents($target), $match)) {
				$this->pingback_url = $match[1];
			} else {
				return $this->xmlrpc_fault(self::RESPONSE_FAULT_TARGET_INVALID);
			}
		}

		$content = file_get_contents($source);
		if ($content !== false) {
			$doc = new \DOMDocument();
			@$doc->loadHTML($content);
			foreach ($doc->getElementsByTagName('a') as $link) {
				if ($link->getAttribute('href') == $target) {
					break;
				}
			}
		} else {
			return $this->xmlrpc_fault(self::RESPONSE_FAULT_SOURCE_LINK);
		}

		return xmlrpc_encode([$this->responses[self::RESPONSE_SUCCESS]]);

		//musí odkazovat na post id
		//pokud v databázi neexistuje záznam o pingback, pak přistoupit na vzdálenou stránku
		//parsování titulku ze vzdálené stránky
	}

	public static function sendPingback($from, $to, $server) {
		$request = xmlrpc_encode_request('pingback.ping', [$from, $to], ['encoding' => 'utf-8']);
		$curl = curl_init($server);
		curl_setopt($curl, CURLOPT_POST, true);
		curl_setopt($curl, CURLOPT_POSTFIELDS, $request);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		$response = curl_exec($curl);
		curl_close($curl);
		return $response;
	}

	private function xmlrpc_fault($faultCode) {
		return xmlrpc_encode([
			'faultCode' => $faultCode,
			'faultString' => $this->responses[$faultCode]
		]);
	}

}

if (!function_exists('http_parse_headers')) {
	function http_parse_headers($raw_headers) {
		$headers = [];
		$key = '';
		foreach (explode("\n", $raw_headers) as $i => $h) {
			$h = explode(':', $h, 2);
			if (isset($h[1])) {
				if (!isset($headers[$h[0]])) {
					$headers[$h[0]] = trim($h[1]);
				} elseif (is_array($headers[$h[0]])) {
					$headers[$h[0]] = array_merge($headers[$h[0]], [trim($h[1])]);
				} else {
					$headers[$h[0]] = array_merge([$headers[$h[0]]], [trim($h[1])]);
				}
				$key = $h[0];
			} else {
				if (substr($h[0], 0, 1) == "\t") {
					$headers[$key] .= "\r\n\t" . trim($h[0]);
				} elseif (!$key) {
					$headers[0] = trim($h[0]);
				}
				trim($h[0]);
			}
		}
		return $headers;
	}
}
<?php declare(strict_types = 1);

namespace App\Routing;

use Nette;
use Nette\Application\IRouter;
use Nette\Application\Request as AppRequest;
use Nette\Http\IRequest as HttpRequest;
use Nette\Http\Url;

class SeoRedirectRouter extends Nette\Object implements IRouter
{

	/** @var array (Presenter:action => slug) */
	private $routingTable;

	/**
	 * @param array $routingTable Presenter:action => [oldSlug]
	 */
	public function __construct(array $routingTable)
	{
		$this->routingTable = $routingTable;
	}

	/**
	 * Maps HTTP request to a Request object.
	 *
	 * @return AppRequest|NULL
	 */
	public function match(HttpRequest $httpRequest)
	{
		$url = $httpRequest->getUrl();
		$slug = rtrim(substr($url->getPath(), strrpos($url->getScriptPath(), '/') + 1), '/');
		foreach ($this->routingTable as $destinationTo => $slugfrom) {
			if (!is_array($slugfrom)) {
				$slugfrom = [$slugfrom];
			}
			foreach ($slugfrom as $oldSlug) {
				if ($slug === rtrim($oldSlug, '/')) {
					$destination = $destinationTo;
					break;
				}
			}
		}

		if (!isset($destination)) {
			return NULL;
		}

		// Front:Single:article,id=75
		$additionalParams = [];
		if (preg_match('~^[^,]+(,(?<paramName>[^,]+)=(?<paramValue>[^,]+))?~', $destination, $matches)) {
			if (isset($matches['paramName'])) {
				$additionalParams[$matches['paramName']] = $matches['paramValue'];
			}
		}
		$destination = preg_replace('~(,.+)?$~', '', $destination);

		$params = $httpRequest->getQuery();
		$pos = strrpos($destination, ':');
		$presenter = substr($destination, 0, $pos);
		$params['action'] = substr($destination, $pos + 1);

		return new AppRequest(
			$presenter,
			$httpRequest->getMethod(),
			$params + $additionalParams,
			$httpRequest->getPost(),
			$httpRequest->getFiles(),
			[AppRequest::SECURED => $httpRequest->isSecured()]
		);
	}

	public function constructUrl(AppRequest $appRequest, Url $refUrl)
	{
		return NULL; // ONE_WAY only
	}

}

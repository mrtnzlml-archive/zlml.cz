<?php

namespace App\RestModule;

use Doctrine;
use Entity\Post;
use Kdyby\Doctrine\EntityManager;
use Model;
use Nette\Application\Responses\JsonResponse;
use Nette\Application\Responses\NotImplementedResponse;
use Nette\Application\UI;
use Nette\Utils\Strings;

/**
 * http://localhost.dev/www.zeminem.cz/www/api/v1/articles:
 * GET: vylistovat URL adresy jednotlivých článků (rovnou články?) + adresu na další stránku při stránkování
 * POST: vytvoření nového článku
 * PUT: přepsání kolekce novou kolekcí článků
 * DELETE: smazání všech článků
 *
 * http://localhost.dev/www.zeminem.cz/www/api/v1/articles/10:
 * GET: vrácení jednoho článku
 * POST: - (nedává smysl)
 * PUT: přepsání (update) článku s ID novým, resp. založení pokud neexistuje
 * DELETE: smazání konkrétního článku
 */
class ArticlesPresenter extends UI\Presenter
{

	/** @var Model\Posts @inject */
	public $posts;

	/** @var EntityManager @inject */
	public $em;

	// http://www.thebuzzmedia.com/designing-a-secure-rest-api-without-oauth-authentication/

	/*
	 * [CLIENT]		- send public API key (user-identifiable information)
	 * [CLIENT]		- generate and send HMAC hash (using private key) - URL! - see http://www.thebuzzmedia.com/designing-a-secure-rest-api-without-oauth-authentication/comment-page-1/#comment-572339
	 * [SERVER]		- get client private key based on public key (from DB)
	 * [SERVER]		- generate HMAC hash (using private key)
	 * [SERVER]		- compare both hashes and return response (or failure)
	 */

	public function actionGet($id = NULL)
	{
		$api_key = $this->getHttpRequest()->getHeader('public-api-key');
		$api_hash = $this->getHttpRequest()->getHeader('api-hash');

		date_default_timezone_set('UTC'); //TODO: -1min
		$date = date('YmdHi');
		$actual_url = $this->getHttpRequest()->getUrl()->getAbsoluteUrl();
		$hmac = hash_hmac('sha512', $actual_url . $date, 'PRIVATE_KEY'); //na základě public key ($api_key)

		$payload = [
			'api_hash' => $this->getHttpRequest()->getHeader('api-hash'),
		];

		$response['method'] = 'GET';
		if ($api_hash !== $hmac) {
			$response['err_code'] = 10;
			$response['err_info'] = 'You are not authorized.';
			$this->sendResponse(new JsonResponse($response));
		} elseif ($id === NULL) {
			$response['data'] = $this->posts->findForApi([]);
			$this->sendResponse(new JsonResponse($response));
		} else {
			$response['data'] = $this->posts->findForApi(['id' => $id], NULL, 1)[0]; //FIXME: vracet 1 result, ne $result[0]
			$this->sendResponse(new JsonResponse($response));
		}
	}

	public function actionPost($id = NULL)
	{
		if ($id !== NULL) {
			$this->sendResponse(new JsonResponse([
				'method' => 'POST',
				'err_code' => 30,
				'err_info' => 'Sorry, this request doesn\'t make any sense.',
			]));
		} else {
			$this->sendResponse(new NotImplementedResponse);

			//TODO: validate vstupních dat (i jestli existují)
			$request = $this->getHttpRequest()->getPost();
			$article = new Post;
			$article->title = $request['title'];
			$article->slug = Strings::webalize($request['title']);
			$article->body = $request['body'];
			$article->date = new \DateTime;
			$article->publish_date = new \DateTime;
			$this->em->persist($article);
			try {
				$this->em->flush($article);
				$this->sendResponse(new JsonResponse([
					'method' => 'POST',
					'success' => $article->id,
				]));
			} catch (Doctrine\DBAL\DBALException $exc) {
				$this->sendResponse(new JsonResponse([$exc->getMessage()])); //FIXME: posílat lépe chyby (ne $exc)!
			}
		}
	}

	public function actionPut()
	{
		$this->sendResponse(new NotImplementedResponse);
	}

	public function actionDelete()
	{
		$this->sendResponse(new NotImplementedResponse);
	}

}

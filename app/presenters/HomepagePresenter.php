<?php

namespace App;

use Cntrl;
use Nette;

class HomepagePresenter extends BasePresenter
{

	///** @var \Model\Xmlrpc @inject */
	//public $xmlrpc;
	///** @var Nette\Http\Request @inject */
	//public $httpRequest;

	/*public function actionXmlrpc() {
		$ping = $this->xmlrpc->pingback_ping('test', $this->link('//Single:about'));
		header('Content-Type: application/xml; charset=utf-8');
		$this->sendResponse(new Responses\TextResponse($ping));

		if ($this->httpRequest->isMethod('POST')) {
			$ping = $this->xmlrpc->pingback_ping($this->link('//Homepage'), $this->link('//Homepage'));
			$this->sendResponse(new Responses\JsonResponse((string)$ping));
		} else {
			$this->redirect('Homepage:');
		}
	}*/

	public function renderDefault()
	{
		$vp = new Cntrl\VisualPaginator($this, 'paginator');
		$paginator = $vp->getPaginator();
		$paginator->itemsPerPage = 10;
		$paginator->itemCount = ITEMCOUNT; //see RouterFactory.php
		$posts = $this->posts->findBy(['publish_date <=' => new \DateTime()], ['date' => 'DESC'], $paginator->itemsPerPage, $paginator->offset);
		$this->template->posts = $posts;
	}

	public function renderRss()
	{
		$this->template->posts = $this->posts->findBy(['publish_date <=' => new \DateTime()], ['date' => 'DESC'], 50);
	}

	public function renderSitemap()
	{
		$this->template->sitemap = $this->posts->findBy(['publish_date <=' => new \DateTime()]);
	}

}

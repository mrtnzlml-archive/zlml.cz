<?php declare(strict_types = 1);

namespace App\Pictures;

use App\{
	AdminModule\Components\AdminMenu\IAdminMenuFactory,
	FrontModule\Components\VisualPaginator\VisualPaginator
};
use Entity;
use Nette;

class PicturesPresenter extends \App\FrontModule\Presenters\BasePresenter
{

	/** @var \App\Pictures\Pictures @inject */
	public $pictures;

	/** @var IAdminMenuFactory @inject */
	public $adminMenuFactory;

	public function renderDefault()
	{
		$vp = new VisualPaginator($this, 'paginator');
		$paginator = $vp->getPaginator();
		$paginator->itemsPerPage = 25; //FIXME: nastavitelný parametr
		$paginator->itemCount = $this->pictures->countBy();
		$this->template->pictures = $this->pictures->findBy([], ['created' => 'DESC'], $paginator->itemsPerPage, $paginator->offset);
	}

	public function handleUploadReciever()
	{
		$uploader = new \UploadHandler();
		$uploader->allowedExtensions = ['jpeg', 'jpg', 'png', 'gif'];
		$uploader->chunksFolder = __DIR__ . '/../../www/chunks';
		$name = Nette\Utils\Strings::webalize($uploader->getName(), '.');
		//TODO: picture optimalization (?)
		$result = $uploader->handleUpload(__DIR__ . '/../../www/uploads', $name);
		try {
			$picture = $this->pictures->findOneBy(['uuid' => $uploader->getUuid()]);
			if (!$picture) { //FIXME: toto není optimální (zejména kvůli rychlosti)
				$picture = new Entity\Picture();
			}
			$picture->uuid = $uploader->getUuid();
			$picture->name = $name;
			$picture->created = new \DateTime('now');
			$this->pictures->save($picture);
		} catch (\Exception $exc) {
			$uploader->handleDelete(__DIR__ . '/../../www/uploads');
			$this->sendResponse(new Nette\Application\Responses\JsonResponse([
				'error' => $exc->getMessage(),
			]));
		}
		//TODO: napřed předat do šablony nová data
		$this->redrawControl('pictures');
		$this->sendResponse(new Nette\Application\Responses\JsonResponse($result));
	}

	public function handleDeletePicture($id)
	{
		$picture = $this->pictures->findOneBy(['id' => $id]);
		@unlink(__DIR__ . '/../../www/uploads/' . $picture->uuid . DIRECTORY_SEPARATOR . $picture->name);
		@rmdir(__DIR__ . '/../../www/uploads/' . $picture->uuid);
		$this->pictures->delete($picture);
		$this->flashMessage('Obrázek byl úspěšně smazán.', 'success');
		$this->redirect('this');
	}

	protected function createComponentAdminMenu()
	{
		return $this->adminMenuFactory->create();
	}

	public function formatLayoutTemplateFiles()
	{
		$name = $this->getName();
		$presenter = substr($name, strrpos(':' . $name, ':'));
		$dir = is_dir(APP_DIR . '/templates') ? APP_DIR : dirname(APP_DIR);
		$list = ["$dir/templates/$presenter/@layout.latte"];
		do {
			$list[] = "$dir/templates/@layout.latte";
			$dir = dirname($dir);
		} while ($dir && ($name = substr($name, 0, strrpos($name, ':'))));
		return $list;
	}

	public function formatTemplateFiles()
	{
		return [__DIR__ . DIRECTORY_SEPARATOR . 'templates' . DIRECTORY_SEPARATOR . "$this->view.latte"];
	}

}

<?php

namespace App;

use Cntrl;
use Entity;
use Kdyby;
use Nette;

class AdminPresenter extends BasePresenter {

	/** @var Pictures @inject */
	public $pictures;
	/** @var Tags @inject */
	public $tags;
	/** @var Users @inject */
	public $users;

	/** @var \PostFormFactory @inject */
	public $postFormFactory;
	/** @var \UserEditFormFactory @inject */
	public $userEditFormFactory;

	private $id = NULL;

	public function startup() {
		parent::startup();
		if (!$this->user->isLoggedIn()) {
			if ($this->user->logoutReason === Nette\Security\IUserStorage::INACTIVITY) {
				$this->flashMessage('Byli jste odhlášeni z důvodu nečinnosti. Přihlaste se prosím znovu.', 'danger');
			}
			$this->redirect('Sign:in', array('backlink' => $this->storeRequest()));
		} elseif (!$this->user->isAllowed($this->name, Authorizator::VIEW)) {
			$this->flashMessage('Přístup byl odepřen. Nemáte oprávnění k zobrazení této stránky.', 'danger');
			$this->redirect('Sign:in', array('backlink' => $this->storeRequest()));
		}
	}

	public function beforeRender() {
		parent::beforeRender();
		$this->template->tagcount = $this->tags->countBy();
		$this->template->usercount = $this->users->countBy();
		if (!$this->user->isAllowed('Admin', Authorizator::EDIT)) {
			$this->flashMessage('Nacházíte se v **demo** ukázce administrace. Máte právo prohlížet, nikoliv však editovat...', 'info');
		}
	}

	public function renderDefault($id = NULL) {
		$this->template->tags = $this->tags->findBy(array());
		if ($id !== NULL) {
			$this->id = $id;
			$this->template->editace = $id;
		}
	}

	public function renderPictures() {
		$this->template->pictures = $this->pictures->findBy(array());
	}

	public function renderPrehled() {
		$this->template->posts = $this->posts->findBy(array(), ['date' => 'DESC']);
	}

	public function renderTags() {
		$this->template->tags = $this->tags->findBy(array());
	}

	public function renderUsers() {
		$this->template->users = $this->users->findBy(array());
	}

	public function renderUserEdit($id = NULL) {
		if ($id !== NULL) {
			$this->id = $id;
		}
		$this->template->account = $this->users->findOneBy(['id' => $id]);
	}

	protected function createComponentUserEditForm() {
		$control = $this->userEditFormFactory->create($this->id);
		$control->onSave[] = function () {
			$this->redirect('users');
		};
		return $control;
	}

	protected function createComponentPostForm() {
		$control = $this->postFormFactory->create($this->id);
		$control->onSave[] = function () {
			$this->redirect('default');
		};
		return $control;
	}

	protected function createComponentColor() {
		$form = new Nette\Application\UI\Form;
		$form->addProtection();
		foreach ($this->tags->findBy(array()) as $tag) {
			$form->addText('color' . $tag->id)
				->setType('color')
				->setValue('#' . $tag->color);
			$form->addSubmit('update' . $tag->id, 'Změnit barvu')
				->onClick[] = function ($this) use ($tag) {
				$this->colorSucceeded($this, $tag->id);
			};
		}
		return $form;
	}

	/**
	 * @param $button
	 * @param $id
	 */
	public function colorSucceeded($button, $id) {
		$vals = $button->getForm()->getValues();
		$newColor = preg_replace('<#>', '', $vals['color' . $id]);
		if (ctype_xdigit($newColor) && (strlen($newColor) == 6 || strlen($newColor) == 3)) {
			try {
				$tag = $this->tags->findOneBy(['id' => $id]);
				$tag->color = $newColor;
				$this->tags->save($tag);
				$this->flashMessage('Tag byl úspěšně aktualizován.', 'success');
			} catch (\Exception $exc) {
				$this->flashMessage($exc->getMessage(), 'danger');
			}
		} else {
			$this->flashMessage("Barva #$newColor není platnou hexadecimální hodnotou.", 'danger');
		}
		$this->redirect('this');
	}

	public function handleUpdate($title, $content, $tags) {
		$texy = new \fshlTexy();
		$texy->addHandler('block', array($texy, 'blockHandler'));
		$texy->tabWidth = 4;
		$texy->headingModule->top = 3; //start at H3
		$texy->headingModule->generateID = TRUE;
		$this->template->preview = Nette\Utils\Html::el()->setHtml($texy->process($content));
		$this->template->title = $title;
		$this->template->tagsPrev = array_unique(explode(', ', $tags));
		if ($this->isAjax()) {
			$this->redrawControl('preview');
		} else {
			$this->redirect('this');
		}
	}

	public function handleDelete($id) {
		try {
			$this->posts->delete($this->posts->findOneBy(['id' => $id]));
			$this->flashMessage('Článek byl úspěšně smazán.', 'success');
		} catch (\Exception $exc) {
			$this->flashMessage($exc->getMessage(), 'danger');
		}
		$this->redirect('this');
	}

	public function handleDeleteTag($tag_id) {
		try {
			$this->tags->delete($this->tags->findOneBy(['id' => $tag_id]));
			$this->flashMessage('Tag byl úspěšně smazán.', 'success');
		} catch (\Exception $exc) {
			$this->flashMessage($exc->getMessage(), 'danger');
		}
		$this->redirect('this');
	}

	public function handleRegenerate($tag_id) {
		try {
			$tag = $this->tags->findOneBy(['id' => $tag_id]);
			$tag->color = substr(md5(rand()), 0, 6); //Short and sweet
			$this->tags->save($tag);
			$this->flashMessage('Tag byl úspěšně regenerován.', 'success');
		} catch (\Exception $exc) {
			$this->flashMessage($exc->getMessage(), 'danger');
		}
		$this->redirect('this');
	}

	public function handleUploadPicture() {
		$uploader = new \UploadHandler();
		$uploader->allowedExtensions = array("jpeg", "jpg", "png", "gif");
		$result = $uploader->handleUpload(__DIR__ . '/../../www/uploads');
		try {
			$picture = new Entity\Picture();
			$picture->uuid = $uploader->getUuid();
			$picture->name = $uploader->getUploadName();
			//$this->pictures->save($picture);
		} catch (\Exception $exc) {
			$uploader->handleDelete(__DIR__ . '/../../www/uploads');
			$this->flashMessage($exc->getMessage(), 'danger');
			$this->sendResponse(new Nette\Application\Responses\JsonResponse(array(
				'error' => $exc->getMessage(),
			)));
		}
		//$this->redrawControl('pictures');
		$this->sendResponse(new Nette\Application\Responses\JsonResponse($result));
	}

}
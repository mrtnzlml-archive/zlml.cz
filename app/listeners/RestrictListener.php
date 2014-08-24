<?php

class RestrictListener extends Nette\Object implements Kdyby\Events\Subscriber {

	public function getSubscribedEvents() {
		return array(
			'App\AdminPresenter::onBeforeRestrictedFunctionality' => 'adminEdit',
//			'Cntrl\PostForm::onBeforeRestrictedFunctionality' => 'controlEdit',
//			'Cntrl\UserEditForm::onBeforeRestrictedFunctionality' => 'controlEdit',
		);
	}

	public function adminEdit(Nette\Application\UI\Presenter $presenter) {
		if (!$presenter->user->isAllowed('Admin', Model\Authorizator::EDIT)) {
			$presenter->flashMessage('Myslím to vážně, editovat opravdu **ne**můžete!', 'danger');
			$presenter->redirect('this');
			return;
		}
	}

	public function controlEdit(Nette\Application\UI\Control $control) {
		if (!$control->presenter->user->isAllowed('Admin', Model\Authorizator::EDIT)) {
			$control->presenter->flashMessage('Myslím to vážně, editovat opravdu **ne**můžete!', 'danger');
			$control->presenter->redirect('this');
			return;
		}
	}

}

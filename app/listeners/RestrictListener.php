<?php declare(strict_types=1);

class RestrictListener extends Nette\Object implements Kdyby\Events\Subscriber
{

	public function getSubscribedEvents()
	{
		return [
			'App\AdminModule\AdminPresenter::onBeforeRestrictedFunctionality' => 'adminEdit',
		];
	}

	public function adminEdit(Nette\Application\UI\Presenter $presenter)
	{
		if (!$presenter->user->isAllowed('Admin:Admin', Model\Authorizator::UPDATE)) {
			$presenter->flashMessage('Myslím to vážně, editovat opravdu **ne**můžete!', 'danger');
			$presenter->redirect('this');
			return;
		}
	}

	public function controlEdit(Nette\Application\UI\Control $control)
	{
		if (!$control->presenter->user->isAllowed('Admin:Admin', Model\Authorizator::UPDATE)) {
			$control->presenter->flashMessage('Myslím to vážně, editovat opravdu **ne**můžete!', 'danger');
			$control->presenter->redirect('this');
			return;
		}
	}

}

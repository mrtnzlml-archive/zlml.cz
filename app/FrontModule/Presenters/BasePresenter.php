<?php declare(strict_types = 1);

namespace App\FrontModule\Presenters;

use App\Settings\Settings;
use Nette;
use Nette\Application\UI;

abstract class BasePresenter extends Nette\Application\UI\Presenter
{

	/** @var Settings @inject */
	public $settings;

	protected $setting;

	public function startup()
	{
		parent::startup();
		$this->template->setting = $this->setting = $this->settings->findAllByKeys();
		$this->template->wfont = $this->getHttpRequest()->getCookie('wfont');
	}

	public function beforeRender()
	{
		$wwwDir = $this->context->parameters['wwwDir']; //yeah, fuck it (use decorator)
		$assetStats = Nette\Utils\Json::decode(file_get_contents($wwwDir . '/dist/webpack-stats.json'));
		foreach ($assetStats->assetsByChunkName->main as $file) {
			if (Nette\Utils\Strings::endsWith($file, 'css')) {
				$this->template->cssFile = $file;
			}
			if (Nette\Utils\Strings::endsWith($file, 'js')) {
				$this->template->jsFile = $file;
			}
		}
	}

	protected function createComponentSignOutForm()
	{
		$form = new UI\Form;
		$form->addProtection();
		$form->addSubmit('logout', 'Odhlásit se')
			->setAttribute('class', 'logout');
		$form->onSuccess[] = function () {
			$this->getUser()->logout();
			$this->flashMessage('Odhlášení bylo úpěšné.', 'info');
			$this->redirect(':Auth:Sign:in');
		};
		return $form;
	}

	/**
	 * @return UI\ITemplate
	 */
	protected function createTemplate()
	{
		/** @var Nette\Bridges\ApplicationLatte\Template $template */
		$template = parent::createTemplate();
		$latte = $template->getLatte();
		$latte->addFilter('texy', function ($input) {
			$texy = $this->prepareTexy();
			$html = new Nette\Utils\Html();
			return $html::el()->setHtml($texy->process($input));
		});
		$latte->addFilter('vlna', function ($string) {
			$string = preg_replace(
				'<([^a-zA-Z0-9])([ksvzaiou])\s([a-zA-Z0-9]{1,})>i',
				"$1$2\xc2\xa0$3", //&nbsp; === \xc2\xa0
				$string
			);
			return $string;
		});
		$latte->addFilter('dateInWords', function ($time) {
			$time = Nette\Utils\DateTime::from($time);
			$months = [
				1 => 'leden',
				2 => 'únor',
				3 => 'březen',
				4 => 'duben',
				5 => 'květen',
				6 => 'červen',
				7 => 'červenec',
				8 => 'srpen',
				9 => 'září',
				10 => 'říjen',
				11 => 'listopad',
				12 => 'prosinec',
			];
			return $time->format('j. ') . $months[$time->format('n')] . $time->format(' Y');
		});
		$latte->addFilter('timeAgoInWords', function ($time) {
			$time = Nette\Utils\DateTime::from($time);
			$delta = round((time() - $time->getTimestamp()) / 60);
			if ($delta === 0) {
				return 'před okamžikem';
			}
			if ($delta === 1) {
				return 'před minutou';
			}
			if ($delta < 45) {
				return "před $delta minutami";
			}
			if ($delta < 90) {
				return 'před hodinou';
			}
			if ($delta < 1440) {
				return 'před ' . round($delta / 60) . ' hodinami';
			}
			if ($delta < 2880) {
				return 'včera';
			}
			if ($delta < 43200) {
				return 'před ' . round($delta / 1440) . ' dny';
			}
			if ($delta < 86400) {
				return 'před měsícem';
			}
			if ($delta < 525960) {
				return 'před ' . round($delta / 43200) . ' měsíci';
			}
			if ($delta < 1051920) {
				return 'před rokem';
			}
			return 'před ' . round($delta / 525960) . ' lety';
		});
		return $template;
	}

	/**
	 * @return \App\Texy\FshlTexy
	 */
	protected function prepareTexy()
	{
		$texy = new \App\Texy\FshlTexy();
		$texy->addHandler('block', [$texy, 'blockHandler']);
		$texy->tabWidth = 4;
		$texy->headingModule->top = 3; //start at H3
		$texy->headingModule->generateID = TRUE;
		$texy->imageModule->root = $this->getHttpRequest()->getUrl()->getBaseUrl() . 'uploads/';
		$texy->imageModule->leftClass = 'leftAlignedImage';
		$texy->imageModule->rightClass = 'rightAlignedImage';
		return $texy;
	}

	/**
	 * Formats layout template file names.
	 * @return array
	 */
	public function formatLayoutTemplateFiles()
	{
		return [__DIR__ . '/Templates/@layout.latte'];
	}

	/**
	 * Formats view template file names.
	 * @return array
	 */
	public function formatTemplateFiles()
	{
		list(, $presenter) = \Nette\Application\Helpers::splitName($this->getName());
		$dir = dirname($this->getReflection()->getFileName());
		$dir = is_dir("$dir/Templates") ? $dir : dirname($dir);
		return [
			"$dir/Templates/$presenter/$this->view.latte",
			"$dir/Templates/$presenter.$this->view.latte",
		];
	}

}

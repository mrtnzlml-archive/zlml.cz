<?php

use Nette\Forms\Controls\TextInput;
use Nette\Forms\Form;
use Nette\Utils\Html;

/**
 * AntispamControl
 * Add basic antispam feature to Nette forms.
 *
 * <code>
 * // Register extension
 * AntispamControl::register();
 *
 * // Add antispam to form
 * $form->addAntispam();
 * </code>
 *
 * @version 0.4
 * @author  Michal Mikoláš <nanuqcz@gmail.com>
 * @license CC BY <http://creativecommons.org/licenses/by/3.0/cz/>
 */
class AntispamControl extends TextInput
{

	/** @var int  minimum delay [sec] to send form */
	public static $minDelay = 5;

	/**
	 * Register Antispam to Nette Forms
	 * @return void
	 */
	public static function register()
	{
		Form::extensionMethod('addAntispam', function (Form $form, $name = 'spam', $label = 'Toto pole vymažte (antispam):', $msg = 'Byl detekován pokus o spam.') {
			// "All filled" protection
			$form[$name] = new AntispamControl($label, NULL, NULL, $msg);

			// "Send delay" protection
			$form->addHidden('form_created', strtr(time(), '0123456789', 'jihgfedcba'))
				->addRule(
					function ($item) {
						if (AntispamControl::$minDelay <= 0) return TRUE; // turn off "Send delay protection"

						$value = (int)strtr($item->value, 'jihgfedcba', '0123456789');
						return $value <= (time() - AntispamControl::$minDelay);
					},
					$msg
				);
			return $form;
		});
	}

	/**
	 * @param string|Html $label
	 * @param int null $cols
	 * @param int null $maxLength
	 * @param string $msg
	 */
	public function __construct($label = '', $cols = NULL, $maxLength = NULL, $msg = '')
	{
		parent::__construct($label, $cols, $maxLength);
		$this->setDefaultValue('Toto pole vymažte (antispam)');
		$this->setAttribute('class', 'form-control input-lg');
		$this->addRule(Form::BLANK, $msg);
	}

	/**
	 * @return TextInput
	 */
	public function getControl()
	{
		$control = parent::getControl();

		$control = $this->addAntispamScript($control);
		return $control;
	}

	/**
	 * @param Html $control
	 *
	 * @return Html
	 */
	protected function addAntispamScript(Html $control)
	{
		$control = Html::el('')->add($control);
		$control->add(Html::el('script', ['type' => 'text/javascript'])->setHtml("
				// Clear input value
				var input = document.getElementById('" . $control[0]->id . "');
				input.value = '';

				// Hide input and label
				if (input.parentNode.parentNode.nodeName == 'TR') {
					// DefaultFormRenderer
					input.parentNode.parentNode.style.display = 'none';
				} else {
					// Manual render
					input.style.display = 'none';
					//var labels = input.parentNode.getElementsByTagName('label');
					var labels = document.getElementsByTagName('label');
					for (var i = 0; i < labels.length; i++) {  // find and hide label
						if (labels[i].getAttribute('for') == '" . $control[0]->id . "') {
							//labels[i].style.display = 'none';
							labels[i].parentNode.style.display = 'none';
						}
					}
				}
			")
		);
		return $control;
	}

}

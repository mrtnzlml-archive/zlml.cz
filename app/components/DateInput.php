<?php

namespace Cntrl;

use Nette\Forms\Form;
use Nette;
use Nette\Utils\Html;

/**
 * Class DateInput
 * @package Cntrl
 */
class DateInput extends Nette\Forms\Controls\BaseControl {

	private $day, $month, $year;

	/**
	 * @param null $label
	 */
	public function __construct($label = NULL) {
		parent::__construct($label);
		//$this->addRule(__CLASS__ . '::validateDate', 'Zadané datum není validní.');
	}

	/**
	 * @param $value
	 * @return Nette\Forms\Controls\BaseControl|void
	 */
	public function setValue($value) {
		if ($value) {
			$date = Nette\DateTime::from($value);
			$this->day = $date->format('j');
			$this->month = $date->format('n');
			$this->year = $date->format('Y');
		} else {
			$this->day = $this->month = $this->year = NULL;
		}
	}

	/**
	 * @return \DateTime|mixed|null
	 */
	public function getValue() {
		return self::validateDate($this)
			? date_create()->setDate($this->year, $this->month, $this->day)
			: NULL;
	}

	public function loadHttpData() {
		$this->day = $this->getHttpData(Form::DATA_LINE, '[day]');
		$this->month = $this->getHttpData(Form::DATA_LINE, '[month]');
		$this->year = $this->getHttpData(Form::DATA_LINE, '[year]');
	}

	/**
	 * Generates control's HTML element.
	 */
	public function getControl() {
		$name = $this->getHtmlName();
		$dayInput = Html::el('div')->addAttributes(array('class' => 'col-lg-4'));
		$dayInput->add(Html::el('input')->name($name . '[day]')->addAttributes(array('class' => 'form-control', 'type' => 'text', 'disabled' => 'disabled'))->id($this->getHtmlId())->value($this->day));
		$monthInput = Html::el('div')->addAttributes(array('class' => 'col-lg-4'));
		$monthInput->add(Nette\Forms\Helpers::createSelectBox(
			array(1 => 'Leden', 'Únor', 'Březen', 'Duben', 'Květen', 'Červen', 'Červenec', 'Srpen', 'Září', 'Říjen', 'Listopad', 'Prosinec'),
			array('selected?' => $this->month)
		)->name($name . '[month]')->addAttributes(array('class' => 'form-control', 'disabled' => 'disabled')));
		$yearInput = Html::el('div')->addAttributes(array('class' => 'col-lg-4'));
		$yearInput->add(Html::el('input')->name($name . '[year]')->addAttributes(array('class' => 'form-control', 'type' => 'text', 'disabled' => 'disabled'))->value($this->year));
		$div = Html::el('div')->addAttributes(array('class' => 'row'));
		return $div->add($dayInput)->add($monthInput)->add($yearInput);
	}

	/**
	 * @param Nette\Forms\IControl $control
	 * @return bool
	 */
	public static function validateDate(Nette\Forms\IControl $control) {
		return checkdate($control->month, $control->day, $control->year);
	}

}
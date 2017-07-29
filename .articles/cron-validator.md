A jak už to tak bývá, tak opět ohnutý pro Nette. Tentokráte inspirovaný řešením ISPConfigu.

# Můžeš tohle, nesmíš tamto


Samotný CRON zápis je velmi rozmanitý a proto se omezím pouze na základní požadavky:

1.  obecně jsou povolené znaky <code>0-9</code>, <code>čárka</code>, <code>*</code>, <code>-</code>, <code>/</code>
2.  <code>čárka</code>, <code>-</code> a <code>/</code> nesmí být nikdy vedle sebe
3.  <code>x</code>, <code>x-y</code>, <code>x/y</code>, <code>x-y/z</code>, <code>*/x</code>, kde x,y,z jsou čísla z povolených časových rozsahů
4.  povolený rozsah pro minuty: <strong>0-59</strong>
5.  povolený rozsah pro hodiny: <strong>0-23</strong>
6.  povolený rozsah pro dny měsíce: <strong>1-31</strong>
7.  povolený rozsah pro měsíce: <strong>1-12</strong>
8.  povolený rozsah pro dny v týdnu: <strong>0-6</strong>

To je myslím slušný výčet pravidel pro zvalidování jednoho příkazu.
Úkolem tohoto článku není ukázat jak tvořit a zpracovávat formulář, ale bude vhodné
umístit sem celý kód alespoň vytvoření:

```php
/**
  * @return Nette\Application\UI\Form
  */
protected function createComponentAddCron() {
	$form = new Nette\Application\UI\Form;
	$form->addProtection();
	$form->addText('minutes', 'Minuty:')
		->addRule(\Fresh\ValidateCron::MINUTES, 'Nevalidní CRON zápis - minuty.');
	$form->addText('hours', 'Hodiny:')
		->addRule(\Fresh\ValidateCron::HOURS, 'Nevalidní CRON zápis - hodiny.');
	$form->addText('mdays', 'Dny měsíce:')
		->addRule(\Fresh\ValidateCron::MDAYS, 'Nevalidní CRON zápis - mdays.');
	$form->addText('months', 'Měsíce:')
		->addRule(\Fresh\ValidateCron::MONTHS, 'Nevalidní CRON zápis - měsíce.');
	$form->addText('wdays', 'Dny v týdnu:')
		->addRule(\Fresh\ValidateCron::WDAYS, 'Nevalidní CRON zápis - wdays.');
	$form->addText('command', 'Příkaz:')
		->setRequired('Vyplňte prosím příkaz, který bude CRON spouštět.');
	$form->addSubmit('save', 'Přidat nový CRON');
	$form->onSuccess[] = $this->addCronSucceeded;
	return $form;
}
```

A rovnou bez hloupých povídání celý validátor:

```php
<?php

namespace Fresh;

/**
 * Class ValidateCron - inspired by ISPConfig
 * @package Fresh
 */
class ValidateCron extends \Nette\Object {

        const MINUTES = '\Fresh\ValidateCron::validateMinutes';
        const HOURS = '\Fresh\ValidateCron::validateHours';
        const MDAYS = '\Fresh\ValidateCron::validateMdays';
        const MONTHS = '\Fresh\ValidateCron::validateMonths';
        const WDAYS = '\Fresh\ValidateCron::validateWdays';

        public static function validateMinutes(\Nette\Forms\IControl $control) {
                return \Fresh\ValidateCron::validateTimeFormat($control->getValue(), 0, 59);
        }

        public static function validateHours(\Nette\Forms\IControl $control) {
                return \Fresh\ValidateCron::validateTimeFormat($control->getValue(), 0, 23);
        }

        public static function validateMdays(\Nette\Forms\IControl $control) {
                return \Fresh\ValidateCron::validateTimeFormat($control->getValue(), 1, 31);
        }

        public static function validateMonths(\Nette\Forms\IControl $control) {
                if($control->getValue() != '@reboot') { // allow value @reboot in month field
                        return \Fresh\ValidateCron::validateTimeFormat($control->getValue(), 1, 12);
                } else {
                        return TRUE;
                }
        }

        public static function validateWdays(\Nette\Forms\IControl $control) {
                return \Fresh\ValidateCron::validateTimeFormat($control->getValue(), 0, 6);
        }

        private static function validateTimeFormat($value, $min_entry = 0, $max_entry = 0) {
                if (preg_match("'^[0-9\-\,\/\*]+$'", $value) == false) { // allowed characters are 0-9, comma, *, -, /
                        return FALSE;
                } elseif (preg_match("'[\-\,\/][\-\,\/]'", $value) == true) { // comma, - and / never stand together
                        return FALSE;
                }
                $time_list = explode(",", $value);
                foreach ($time_list as $entry) {
                        // possible value combinations:
                        // x               =>      ^(\d+)$
                        // x-y             =>      ^(\d+)\-(\d+)$
                        // x/y             =>      ^(\d+)\/([1-9]\d*)$
                        // x-y/z           =>      ^(\d+)\-(\d+)\/([1-9]\d*)$
                        // */x             =>      ^\*\/([1-9]\d*)$
                        // combined regex  =>      ^(\d+|\*)(\-(\d+))?(\/([1-9]\d*))?$
                        if (preg_match("'^(((\d+)(\-(\d+))?)|\*)(\/([1-9]\d*))?$'", $entry, $matches) == false) {
                                return FALSE;
                        }
                        // matches contains:
                        // 1       =>      * or value or x-y range
                        // 2       =>      unused
                        // 3       =>      value if [1] != *
                        // 4       =>      empty if no range was used
                        // 5       =>      2nd value of range if [1] != * and range was used
                        // 6       =>      empty if step was not used
                        // 7       =>      step
                        if ($matches[1] == "*") {
                                // not to check
                        } else {
                                if ($matches[3] < $min_entry || $matches[3] > $max_entry) { // check if value is in allowed range
                                        return FALSE;
                                } elseif (isset($matches[4]) && ($matches[5] < $min_entry || $matches[5] > $max_entry || $matches[5] <= $matches[3])) {
                                        // check if value is in allowed range and not less or equal to first value
                                        return FALSE;
                                }
                        }
                        if (isset($matches[6]) && ($matches[7] < 2 || $matches[7] > $max_entry - 1)) { // check if step value is valid
                                return FALSE;
                        }
                } // end foreach entry loop
                return TRUE;
        }

}
```

Validátorem navrácené errory lze vykreslit například takto ručně (nově v DEV Nette):

```html
{form $form}

<ul class="error" n:if="$form->allErrors">
        <li n:foreach="$form->allErrors as $error">{$error}</li>
</ul>

...

{/form}
```
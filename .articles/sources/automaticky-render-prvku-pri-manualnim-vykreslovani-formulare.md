---
timestamp: 1475925989000
title: Automatický render prvků při manuálním vykreslování formuláře
slug: automaticky-render-prvku-pri-manualnim-vykreslovani-formulare
---
Je čas na nějakou tu Nette divočinu. Podívejme se pod drobnohledem na to, jak funguje vykreslování prvků formuláře. Nejprve si vytvoříme úplně jednoduchý formulář, který bude obsahovat jeden prvek:

```php
protected function createComponentContactForm()
{
	$form = new UI\Form();
	$form->addSubmit('send', 'Odeslat');
	$form->onSuccess[] = function (UI\Form $form, $values) {
		throw new \Nette\NotImplementedException;
	};
	return $form;
}
```

Jak bude vypadat takový formulář když jej vykreslíme pomocí `{control contactForm}`? Ve výchozím projektu asi nějak takto:

```html
<form action="/web-project/www/" method="post" id="frm-contactForm">
<table><tbody><tr>
	<th></th>
	<td><input type="submit" name="send" value="Odeslat" class="button"></td>
</tr></tbody></table>
<input type="hidden" name="_do" value="contactForm-submit">
</form>
```

Aniž se budeme nořit do detailů, tak je zřejmé, že se kromě našeho odesílacího inputu vyrenderoval i nějaký hidden input automaticky. Než se posuneme dále z tohoto úvodu, zjistíme ještě, co se stane při manuálním vykreslování:

```latte
{form contactForm}
	{input send}
{/form}
```

Ačkoliv to není nikde dané, i v tomto případě se vykreslil další input prvek automaticky:

```html
<form action="/web-project/www/" method="post" id="frm-contactForm">
	<input type="submit" name="send" value="Odeslat">
	<input type="hidden" name="_do" value="contactForm-submit">
</form>
```

# Chytrý antispam prvek

Po krátkém úvodu (který je zcela jistě každému jasný) se přesuneme k něčemu zdánlivě jinému. Vytvoříme si chytrý antispam prvek a zjistíme, jak se bude při renderování chovat a jestli se vždy chová předvídatelně. A asi nebude překvapením, že se v určité situaci zachová moc moc špatně. Jedná se o delší kód, takže jej [najdete na Gistu](https://gist.github.com/mrtnzlml/95ac7726cf2788d83e3c87bc97dbef3a). Prakticky nejde o nic jiného, než že si vytvoříme vlastní antispam prvek. Tento prvek funguje tak, že vytvoří input ve kterém je nějaký text a pokud je k dispozici javascript, tak jej schová a smaže obsah. V tom případě je kontrola v pořádku, protože tiše předpokládá, že útočníkův robot nebude umět JS. Ačkoliv se to v dnešní době může zdát jako absurdní, tak to pořád funguje velmi dobře. Navíc přidává ještě pár honeypotů a doufá, že někde robot uvízne. Důležité je, že nijak neobtěžuje běžného návštěvníka - prostě to není vůbec vidět.

Napíšeme si jednoduché rozšíření pro DI kontejner, aby bylo možné tento nový prvek používat:

```php
<?php

namespace App;

use Nette\Forms\Form;

class HoneypotExtension extends \Nette\DI\CompilerExtension
{

	public function afterCompile(\Nette\PhpGenerator\ClassType $class)
	{
		$init = $class->methods['initialize'];
		$init->addBody(self::class . '::registerControlExtensionMethods();');
	}

	public static function registerControlExtensionMethods()
	{
		Form::extensionMethod('addAntispam', function (Form $form, $name = 'honeypot', $label = 'Vymažte toto pole') {
			$form[$name . '_1'] = new \App\Forms\AntispamControl($name, $label);
			return $form;
		});
	}

}
```

Toto rozšíření samozřejmě zaregistrujeme v konfiguračním souboru:

```neon
extensions:
	- App\HoneypotExtension
```

A je to - v našem původním formuláři můžeme použít nový prvek:

```php
$form = new UI\Form();
$form->addAntispam();
//...
```

Je to trošku magie a IDE si s tím neporadí. Proto nebude našeptávat. V tomto případě doporučuji napsat si nějakou vlastní `FormFactory`, která bude vytváře instance `UI\Form` a do této třídy doplnit anotaci `@method addAntispam()`. Udělat si vlastní továrničku na `UI\Form` není vůbec špatný nápad a to nejen pro antispam. Just do it.

Tak a teď když máme funkční antispam a honeypoty formuláře, je čas kouknout se co se děje při renderování. Nejdříve automatické - pomocí `{control contactForm}`. Zde není co řešit. Prostě se všechny potřebná políčka vyrenderují a vše je tak, jak by mělo být. A co manuální vykreslování? Zde začíná ta nepříjemná část. Nette nemůže vědět, že by měl automaticky vykreslit i další prvky, takže je prostě nevykreslí. Praktická zkušenost je taková, že při manuálním vykreslování prostě antispam přestane fungovat. Zde se musíme ještě zasnažit.

# Chytřejší antispam prvek

Teď musíme jít fakt na dřeň problému. Jak vlastně funguje to automatické renderování inputu při manuálním vykreslování? Existuje něco jako třída `Nette\Bridges\FormsLatte\Runtime`, která má dvě metody: `renderFormBegin` a `renderFormEnd`. Právě druhá zmíněná přidává nějaký vlastní kód a je jedno, jestli se jedná o manuální, nebo automatické renderování formuláře. To by se nám hodilo. Vytvořme si tedy vlastní `Runtime` třídu. Není potřeba aby tato třída dědila od původní. Stačí, když si vykopírujeme metodu `renderFormEnd` a lehounce pozměníme prostřední foreach:

```php
foreach ($form->getControls() as $control) {
	if ($control->getOption('autorender') || ($control->getOption('type') === 'hidden' && !$control->getOption('rendered'))) {
		$s .= $control->getControl();
	}
}
```

Přesně tak. Všechny čáry a kouzla se aktivují, když nastavíme prvkům `autorender` ([viz Gist](https://gist.github.com/mrtnzlml/95ac7726cf2788d83e3c87bc97dbef3a)). Jenže kde se tato třída původně používala? Kde ji použijeme nyní?

Řešení najdeme opět v namespace `Nette\Bridges\FormsLatte`, tentokrát však ve třídě `FormMacros`, která registruje formulářová makra. Vytvoříme si tedy vlastní implementaci `FormMacros` třídy, která bude dědit od původní:

```php
class FormMacros extends \Nette\Bridges\FormsLatte\FormMacros
{

	public static function install(\Latte\Compiler $compiler)
	{
		$me = new static($compiler);
		$me->addMacro('form', [$me, 'macroForm'], 'echo \App\Forms\Runtime::renderFormEnd(array_pop($this->global->formsStack));');
		$me->addMacro('formContainer', [$me, 'macroFormContainer'], 'array_pop($this->global->formsStack); $formContainer = $_form = end($this->global->formsStack)');
		$me->addMacro('label', [$me, 'macroLabel'], [$me, 'macroLabelEnd'], NULL, self::AUTO_EMPTY);
		$me->addMacro('input', [$me, 'macroInput']);
		$me->addMacro('name', [$me, 'macroName'], [$me, 'macroNameEnd'], [$me, 'macroNameAttr']);
		$me->addMacro('inputError', [$me, 'macroInputError']);
	}

}
```

Důležitá je zde registrace makra `{form}`, protože to pro svojí uzavírací značku používá novou implementaci `Runtime` (tu která podporuje autorender). Zaregistrujeme:

```neon
latte:
	macros:
		- App\Forms\FormMacros::install
```

Smažeme cache a profitujeme. Nyní se autospam vykresluje automaticky i při manuálním renderování... :)

Podobně lze samozřejmě přidat i další kontroly jako je například kontrolní součet dvou čísel, který se javascriptem předvypočte automaticky, ale s vypnutým javascriptem se zobrazí políčka pro uživatele. Řešení tohoto antispamu je také [na mém Gistu](https://gist.github.com/mrtnzlml/961c3e2368e98aaa433e02c6603a5086). Jen je potřeba trošku rozšířit `HoneypotExtension`:

```php
public static function registerControlExtensionMethods()
{
	Form::extensionMethod('addAntispam', function (Form $form, $name = 'honeypot', $label = 'Vymažte toto pole') {
		$form[$name . '_1'] = new \App\AntispamControl($name, $label);

		$first = round(rand(0, 900), -2);
		$second = rand(0, 99);
		$validationData = self::encodeNumber($first) . ';' . self::encodeNumber($second);
		$form->addHidden('validationData', $validationData)->setOmitted(TRUE)->setOption('autorender', TRUE);
		$form[$name . '_3'] = new \App\Forms\SumAntispamControl($first, $second, $validationData);

		return $form;
	});
}

private static function encodeNumber($originalNumber)
{
	return strtr($originalNumber, '0123456789', '(_.!)@-*+&'); //cannot contain ';' character
}
```

Původní kód zůstává stejný, jen jsem přidal další kontrolu a zakódoval číslice tak, aby nebylo jednoduché na první pohled poznat princip tohoto antispamu. Ve fantazii se meze nekladou.

A právě v tom je možná trošku problém. Zejména kvůli autorender funkci je potřeba zasahovat do vnitřností Nette a zde je již na pováženou, jestli je to dobře či nikoliv. Velké úskalí vidím v tom, že si programátor vyměňuje stavební kameny Nette za svoje trošku upravené a to nemusí být vždy hned evidentní. Pak je na zamyšlenou, jestli by nestálo za to vytvořit PR. Kdo by ale stál o takovou hovadinu... :)
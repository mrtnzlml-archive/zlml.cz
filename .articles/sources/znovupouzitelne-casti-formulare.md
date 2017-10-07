---
id: d0a905ac-329a-46a9-899b-bfe7518519c4
timestamp: 1448405176000
title: Znovupoužitelné části formuláře
slug: znovupouzitelne-casti-formulare
---
Před nějakým časem jsem psal o tom, jak vytvořit [znovupoužitený formulář](znovupouzitelny-formular). Nejedná se o nic jiného, než o správné navržení a následné použití komponent, tedy potomků `UI\Control`. Pokud bych měl být upřímný, nemyslím si, že se formuláře nějak často na webu opakují a osobně tento princip používám spíše pro oddělení části aplikace do samostatného balíčku. Tím spíš najde následující ukázka méně použití. Právě mám totiž za úkol navrhnout předělání jedné administrace. Úkolem není hledět na to, jak moc je tento přístup špatný, ale navrhnout řešení, které nahradí stávající 1:1. Tato administrace obsahuje často se opakující (a velmi rozsáhlý) formulář, který se skládá z několika karet. Navíc některé části formuláře spolu vůbec nesouvisí a na každé stránce je formulář trošku jiný (i když je podobnost zřejmá). Vzhledem k tomu, že se jedná o tak rozsáhlý kód, upustil jsem od znovupoužitelného formuláře a navrhnul jsem znovupoužitelné pouze jeho části. A na následujících řádcích nastíním jak.

# Na začátku stála komponenta

Pořád platí, že je samotný formulář komponenta. Na tom se nic nemění. V mém případě se však hodilo udělat si ještě nějaké bázové třídy. Pokusím se ukázky ořezat co nejvíce od zbytečností tak, aby to pokud možno ještě dávalo smysl:

```php
class NewsForm extends BaseControl {

	/** @var News|NULL */
	private $news;

	public function __construct($news) {
		parent::__construct();
		$this->news = $news;
	}

	public function render() {
		$this->template->render(__DIR__ . '/NewsForm.latte');
	}

	protected function createComponentNewsForm() {
	    $form = $this->form;
	    // nastavení společných prvků formuláře
	    return $form
	}

}
```

K tomu (třeba) nějaká ta generovaná továrnička a komponenta tak jak ji známe všichni je hotova. Bude však nutné rozklíčovat, co se děje třeba pod třídou `BaseControl`. Jedná se o jednoduchého předka, který krom dalších věcí obsahuje hlavně toto:

```php
abstract class BaseControl extends UI\Control {

	/** @var BaseForm */
	protected $form;

	public function __construct() {
		parent::__construct();
		$this->form = new BaseForm;
	}

	protected function attached($obj) {
		parent::attached($obj);
		if ($obj instanceof UI\Presenter) {
			$this->form->addComponent(new SubmitButtonsContainer, 'submitButtons');
			$this->form->addComponent(new AnotherContainer, 'another');
		}
	}

}
```

Zde se vytvoří nějaký formulář (s kterým pak pracuji v komponentě) a po připojení formuláře k presenteru se připojí i nějaké formulářové kontejnery. Než se však k těmto kontejnerům dostanu, tak by bylo dobré prozradit i co se děje v třídě `BaseForm`. Popravdě nic moc:

```php
/**
 * @method addTinyMCE($name, $label = NULL, $cols = NULL, $rows = NULL)
 */
class BaseForm extends UI\Form {

	/** @var callable[] */
	public $onSaveAndStay;

	/** @var callable[] */
	public $onSaveAndExit;

	/** @var callable[] called BEFORE onClick event */
	public $onSuccess;

	public function __construct() {
		parent::__construct();
		$this->addProtection();
	}

}
```

Nastavím si zde nějaké věci, které jsou pro každý formulář v administraci obecně společné. Konkrétně tedy CSRF ochranu a pár polí pro události. Události jsem si zde musel nadefinovat sám, běžně se na formuláři volá `onSuccess` událost až po `onClick` ([link](https://api.nette.org/2.3.7/source-Forms.Form.php.html#380-420)), ale zrovna zde jsem to potřeboval obráceně. Hodí se to v okamžiku, kdy chci využívat `onSuccess`, ale v `onClick` už z formuláře třeba přesměrovávám pryč. Vzhledem k tomu, že oba eventy se volají jen při validním odeslání, tak to ničemu nevadí. V této třídě je také vhodné místo pro umístění nějakých dynamických metod do anotací, aby je IDE dobře napovídalo (viz `addTinyMCE`). Byl to dlouhý úvod, ale vše je připraveno a můžeme se vrhnout na kontejnery.

# Formulářové kontejnery

Osobně [formulářové kontejnery](https://pla.nette.org/cs/dedicnost-vs-kompozice) nemám moc rád. Jsou sice super, ale pohybují se na další úrovni formuláře. Pokud se však s tímto faktem smíříme (a nejlépe z něj uděláme výhodu), pak jsou docela super a zde se skvěle hodí. Můžu si pěkně oddělit například odesílací tlačítka a ty pak vesele používat ve všech formulářích:

```php
class SubmitButtonsContainer extends BaseFormContainer {

	private $form;

	public function attached($obj) {
		parent::attached($obj);
		if ($obj instanceof BaseForm) {
			$this->form = $obj;
			$obj->onSuccess[] = function (BaseForm $form) {
				$path = $this->lookupPath(BaseForm::class);
				dump($form->getValues()->$path); // další zpracování hodnot
			};
		}
	}

	public function render() {
		$this->template->_form = $this; // kvůli formulářovým makrům v šabloně
		$this->template->render(__DIR__ . '/SubmitButtonsContainer.latte');
	}

	protected function configure() {
		$this->addSubmit('saveAndStay', 'Uložit a zůstat')->onClick[] = function (SubmitButton $button) {
            $form = $button->getForm();
            $this->form->onSuccess($form, $form->getValues());
            $this->form->onSaveAndStay($form, $form->getValues());
		};
	}

}
```

Pokud sledujete fórum, tak vám je tento návrh jistě povědomý. Jedná se o [slavné řešení pod čarou](https://forum.nette.org/cs/11747-skladani-komponent-a-formulare#p84652). Přesně toto se odehrává v rodičovské třídě `BaseFormContainer`. Doplnil jsem si do této třídy však jednu malou vychytávku. Chtěl jsem totiž, aby každý kontejner mohl mít vlastní šablonu. To běžně není možné. Kontejner tedy mohu vykreslovat pomocí dobře známého makra `{control ...}` (což nedělá nic jiného, než že se zavolá metoda `render`). Jenže co je `$this->template`? Bázový kontejner jsem musel rozšířit ještě o vhodnou část z `UI\Control`, která se stará o vykreslování:

```php
abstract class BaseFormContainer extends Forms\Container {

	/** @var UI\ITemplateFactory */
	private $templateFactory;

	/** @var UI\ITemplate */
	private $template;

    public function injectTemplateFactory(UI\ITemplateFactory $templateFactory) {
		$this->templateFactory = $templateFactory;
	}

	abstract public function render();

    public function getTemplate() {
		// bla bla, mrkni na: https://api.nette.org/2.3.7/source-Application.UI.Control.php.html#45
		return $this->template;
	}

	protected function createTemplate() {
		/** @var UI\ITemplateFactory $templateFactory */
		$templateFactory = $this->templateFactory ?: $this->lookup(UI\Presenter::class)->getTemplateFactory();
		return $templateFactory->createTemplate(NULL);
	}
```

Jednoduché vykreslitelné formulářové kontejnery. Cool. Abych to rychle zrekapituloval. Máme jednoduchou komponentu na formulář, která dědí od `BaseControl`. Tato třída připojuje ve vhodný čas formulářové kontejnery, které se umí vykreslit (což běžně nejde).

V šabloně `SubmitButtonsContainer.latte` je možné používat normálně `{input ...}` makra a další, jako kdybych pokračoval dál v šabloně jednoho velkého formuláře. Samotné připojené formulářové kontejnery je možné vykreslovat pomocí klasického makra `{control newsForm-submitButtons}` v hlavním formuláři. To je možná trošku nevýhoda, protože kontejnery se připojují do formuláře a stávají se tak podkomponentou. Musím tedy control makro volat stylem *rodič-podkomponenta*.

# Znovupoužitelnost vykreslitelných kontejnerů

Kde je ta znovupoužitelnost? Jak bych udělal to, že použiju třeba odesílací tlačítka (nebo jakoukoliv jinou část formuláře) někde jinde? Jednoduše. Prostě vytvoříme formulář (to je podmínka nutná) a kontejner v něm použijeme:

```php
protected function createComponentTest() {
    $form = new UI\Form;
    $form->addComponent(new AnotherContainer, 'another');
    $form->addSubmit('odeslat', 'Odeslat');
    $form->onSuccess[] = function ($_, ArrayHash $values) {
        dump($values);
    };
    return $form;
}
```

K tomu třeba nějaká taková šablona:

```
{form test}
    {control test-another}
    {input odeslat}
{/form}
```

Formulář se samozřejmě vykreslí i při obyčejném `{control test}`, ale bez šablony kontejneru (píšu si nápad na vylepšení). Vlastně je ta myšlenka docela jednoduchá, že? Jen je třeba dát pozor na to, že kontejner takto umisťuje formulářové prvky na jinou úroveň.

Malá poznámka na závěr, která je sice mimo, ale může se hodit. Občas je potřeba zajistit si někde inject závislostí, ale z nějakého důvodu je to problematické. Může se jednat třeba o závislost v abstraktní rodičovské třídě. V takovém případě je možné v configu nastavit [decorator](https://github.com/dg/nette-di/blob/master/tests/DI/DecoratorExtension.basic.phpt):

```neon
decorator:
	BaseFormContainer:
		inject: on
```

<del>Příště se podíváme znovu na [Dependent select box](dependent-select-box). Původní článek si totiž zaslouží důkladnou revizi a po krátké anketě jsem byl přesvědčen, že bude lepší napsat nový článek a podívat se na celý problém podrobněji.</del> <span style="color:green">Podívejte se raději na <a href="https://github.com/NasExt/DependentSelectBox">tento doplněk</a>, který závislý select box řeší jinak - možná lépe.</span>
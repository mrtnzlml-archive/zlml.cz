---
timestamp: 1399974862000
title: Znovupoužitelný formulář
slug: znovupouzitelny-formular
---
Každý kdo nějakou chvíli pracuje s Nette Frameworkem již jistě narazil na prvky, které lze použít v aplikaci opakovaně. Říkejme jim znovupoužitelné komponenty, nebo prostě jen komponenty. Následující text má za úkol ukázat opět maximálně prakticky a uceleně jedno z možných řešení jak k tomuto problému přistupovat a co se mi na tomto řešení líbí, nebo naopak nelíbí.

# Motivace

Proč vůbec přemýšlet o komponentách? Na tuto věc má pravděpodobně dost lidí zcela jiný názor. Pro mě je havní motivací to, že si vyčistím presentery. Chci toho mít v presenterech skutečně tak akorát. Vždyť podle MVP struktury bych v presenterech neměl mít nic složitého. A pak je zde ta znovupoužitelnost. Co když se rozhodnu, že potřebuji použít stejný formulář na více místech? Přece ho nebudu kopírovat. A že takový požadavek je sice často někdo zmíní, ale prakticky není moc reálný? Ale kdepak. Zrovna nedávno jsem vytvářel mnoho stránek a na každé jsem chtěl mít právě kontaktní formulář. To je požadavek na komponentu jako dělaný...

# Presenter

Vždy když programuji něco takového, tak se nedívám na to, jak je to správně z hlediska OOP a bůh ví čeho všeho ještě. Pro mě je v současné chvíli klíčové to, jak se dané řešení bude používat. Přesenter by tedy mohl vypadat třeba takto:

```php
<?php

class AdminPresenter extends BasePresenter {

	/** @var \ActualityFactory @inject */
	public $actualityFactory;

	private $id;

	public function actionAktualita($id = NULL) {
		$this->id = $id;
	}

	protected function createComponentForm() {
		$control = $this->actualityFactory->create($this->id);
		$control['form']->onSuccess[] = function () {
			$this->redirect('default');
		};
		return $control;
	}

}
```

Mám tedy presenter, který je například pro administraci a jednu podstránku, která bude sloužit jak pro editaci aktuality, tak i pro přidání nové aktuality. Proto je parametrem *action* právě `id = NULL`. Tim totiž říkám, že může přijít jako parametr *ID* aktuality (v tom případě ji budu chtít upravovat), nebo toto *ID* nebude předáno a v tom případě bude hodnota *NULL* a budu s tím později patřičně nakládat.

V poslední metodě si daný formulář vytvořím. Teoreticky by stačila pouze první řádka. Přidávám však ještě další chování do pole `onSuccess[]`, protože chci komponentu používat na více místech, ale pokaždé po úspěchu přesměrovat na jinou stránku. Zde je jedna z věcí které se mi nelíbí. A to je právě to volání `$control['form']->onSuccess[] = ...`. Musím totiž vědět jak je pojmenovaný formulář uvnitř komponenty, což mě ale je skutečnosti vůbec nezajímá. Mnohem raději bych byl třeba za zápis `$control->onSuccess[] = ...`. Chápu, že se nemusí použít komponenta pouze na fomulář, ale přesto. **Neznáte někdo lepší řešení?**

A teď přijde záludná otázka. Co je to ta factory a proč jí vlastně používám?

# Factory

Protože v komponentách velmi často potřebuji nějaké závislosti, musím je tam nějak dostat. K tomu slouží například generované továrničky. Jedná se vlastně pouze o interface:

```php
<?php

interface IActualityFactory {

	/** @return \Cntrl\Actuality */
	public function create();

}
```

Nette se této továrničky chytí a vytvoří zhruba tento kód:

```php
<?php

final class SystemContainer_IActualityFactoryImpl_58_IActualityFactory implements IActualityFactory {

	private $container;

	public function __construct(Nette\DI\Container $container) {
		$this->container = $container;
	}

	public function create() {
		$service = new Cntrl\Actuality;
		return $service;
	}

}
```

Zde je tedy (když bude potřeba) možné vytvořit nějaké závislosti a v metodě `create()` je komponentě předat. To vše lze udělat automaticky a Nette se o to postará. Dokonce lze předávat i parametry z konfiguračního souboru. Je to nesmírně elegantní řešení. Kdybych továrničky nepoužil, musel bych vytvářet ručně komponentu, to by ale znamenalo, že bych také musel předávat všechny závislosti ručně a jen bych si tím přidělal práci. Zkuste si vytvořit komponentu bez použití factory. Je nesmysl tahat si v presenteru nepotřebné závislosti přes presenter... Jak však předat parametry z presenteru? Netuším, jestli to lze nějak generovanou továrničku naučit, nic nám však nebrání napsat si vlastní factory:

```php
<?php

class ActualityFactory extends Nette\Object {

	private $actualities;

	public function __construct(App\Actualities $actualities) {
		$this->actualities = $actualities;
	}

	public function create($id) {
		return new \Cntrl\Actuality($this->actualities, $id);
	}

}
```

Je jasně vidět, že tato ručně napsaná factory vypadá velmi podobně jako automaticky vygenerovaná, ale navíc teď můžu předat metodě `create($id)` parametr, což je přesně to co potřebuji viz presenter. Chci si předat číslo aktuality a nevidím důvod proč bych to měl dělat až v komponentě. Komponenta potřebuje *ID* aktuality, ale kde ho vezme nemusí řešit. Je otázka, jestli bych si už rovnou neměl vytáhnout data z databáze v presenteru. Takto se mi to ale zatím líbí víc...

Ručně vytvořenou factory musíme ještě zaregistrovat v konfiguračním souboru:

```neon
services:
	- ActualityFactory
```

To je vše. Už jsme pouze krůček od funkčního formuláře.

# Samotná komponenta

Samotný kód komponenty už není téměř ničím zvláštní, nebo nějak nepochopitelný:

```php
<?php

namespace Cntrl;

use App;
use Entity;
use Nette\Application\UI;
use Nette;

class Actuality extends UI\Control {

	private $actualities;
	private $actuality;

	public function __construct(App\Actualities $actualities, $id) {
		parent::__construct();
		$this->actualities = $actualities;
		$this->actuality = $this->actualities->findOneBy(['id' => $id]);
	}

	public function render() {
		$this->template->setFile(__DIR__ . '/Actuality.latte');
		$this->template->render();
	}

	protected function createComponentForm() {
		$form = new UI\Form;
		$form->addText('headline', 'Titulek:');
		$form->addTextArea('text', 'Text:');
		$form->addSubmit('send', 'Uložit');
		if($this->actuality) { //výchozí hodnoty jen pokud aktualita již existuje
			$form->setDefaults(array(
				'headline' => $this->actuality->headline,
				'text' => $this->actuality->text,
			));
		}
		$form->onSuccess[] = $this->actualityFormSucceeded;
		return $form;
	}

	public function actualityFormSucceeded(UI\Form $form) {
		$values = $form->getValues();
		try {
			if(!$this->actuality) { //pokud ještě neexistuje vytvořím novou entitu
				$this->actuality = new Entity\Actuality();
			}
			$this->actuality->headline = $values->headline;
			$this->actuality->text = $values->text;
			$this->actuality->date = new \DateTime();
			$this->actualities->save($this->actuality);
			$this->presenter->flashMessage('Aktualita byla úspěšně uložena.', 'success');
		} catch (\Exception $exc) {
			$this->presenter->flashMessage($exc->getMessage(), 'danger');
		}
		//žádný redirect, až v presenteru!
	}

}
```

V konstruktoru jednak získám nějakou modelovou třídu pro práci s databází (o to se krom factory  vůbec nestarám) a také *ID*, které přišlo z presenteru. A rovnou toto *ID* použiju k vytáhnutí si dat z databáze, protože konstruktor se spustí vždy a já také vím, že tyto data budu vždy potřebovat. V `render()` metodě pouze předám šablonu komponenty, která ve své nejprimitivnější podobě může vypada pouze takto:

```
{control form}
```

Ostatně stejný kód mohu použít pro šablonu do které předávám komponentu z presenteru. Výsledkem celého snažení je tedy poměrně jednoduchý přesenter a jedna stránka na které je formulář, který zvládne jak editaci, tak vytváření nového záznamu v databázi.

Používáte nějaké lepší (jiné) řešení? Podělte se o něj... :-)
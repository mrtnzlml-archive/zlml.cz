Velká část článků na tomto blogu jsou reakcí na nějaký reálný problém. Nehledě na to, kde jsem (třeba na včerejším [React workshopu](http://blog.id-sign.com/react-workshop/)), tak odpovídám na dotazy ohledně Nette. Je to v pořádku, rád pomůžu. Jsou však dotazy, které se neustále opakují. Toto je jeden z nich:

> Formuláře v Nette jsou strašný voser. Jak to dělat lépe?

Většinou takto vágně to vždy začíná. Následuje stejné kolečko - vysvětlím proč se to tak dělá, popíšu jiný přístup k formulářům, tazatel je spokojen, podívám se do dokumentace, že to tam opravdu je a čekám na další stejný dotaz. Proto si teď dovolím vyzdvihnout nad rámec dokumentace několik základních způsobů, jak s formuláři v Nette pracovat.

# První způsob - líný Ota

Tento způsob zná asi úplně každý. Pro superrychlé vytvoření formuláře jej stačí nadefinovat v presenteru nějak takto:

```php
protected function createComponentComplicatedForm(): \Nette\Application\UI\Form
{
  $form = new \Nette\Application\UI\Form;
  $form->addSelect('selectNo1', NULL, ['Item 1', 'Item 2']);
  $form->addSubmit('send', 'Odeslat');
  $form->onSuccess[] = function (\Nette\Application\UI\Form $form, \stdClass $values) {
    bdump($values);
    throw new \Nette\NotImplementedException;
  };
  return $form;
}
```

A použít v příslušné šabloně pomocí `control` makra:

```
{control complicatedForm}
```

Je to velmi jednoduché řešení a prakticky se zase až tolik nepoužívá. Většina lidí přijde poměrně rychle na to, že potřebují daleko větší flexibilitu, kterou nabízí další způsob v pořadí. Podívejme se však co se děje když se takový formulář odešle. Plyne z toho totiž jedna důležitá vlastnost, na kterou bude potřeba myslet později.

Formulář se odesílá jako takový zvláštní signál s těmito POST daty:

```
selectNo1=0&send=Odeslat&_do=complicatedForm-submit
```

V průběhu životního cyklu presenteru (před `beforeRender`) dojde ke zpracování tohoto signálu. Konkrétně se nad formulářem zavolá metoda `signalReceived` resp. v případě formuláře `fireEvents`. Poměrně záhy se zavolají základní validace všech formulářových prvků. Schválně jsem v příkladu zvolil select, protože je na něm hezky vidět, že kontroluje co uživatel odeslal v selectu za hodnoty. Pokud je odeslaná hodnota k dispozici v předem nadefinovaném formuláři, tak se vybere. Co se stane pokud uživatel (záškodník) odešle něco jiného?

```
selectNo1=666&send=Odeslat&_do=complicatedForm-submit
```

V tomto případě metoda `getValue` vrátí hodnotu `NULL` a validace takového formuláře nebude úspěšná. To vyústí v chybovou hlášku `Please select a valid option.` - automaticky. Asi známá vlastnost a do chvíle než řeknu jinak bude platit.

# Druhý způsob - nešťastný Karel

Zde se většina lidí zasekne a nádává. Protože vykreslování formuláře je v předchozím případě moc kostrbatá a vlastní PHP rendery nejsou moc nápomocné, přistoupíme k ručnímu vykreslování (místo `control` makra):

```
{form complicatedForm}
  {input send}
{/form}
```

Už tady si většina lidí alespoň jednou vyláme zuby (já to dělám pravidelně). Předchozí kód totiž **nebude fungovat**. Formulář se ke vší smůle sice odešle, ale neudělá vůbec nic. Pro správné fungování je nutné vykreslit všechny formulářové prvky:

```
{form complicatedForm}
  {input selectNo1}
  {input send}
{/form}
```

Proč? Co se děje? Tato definice formuláře totiž není správná. Nette ví, že má být ve formuláři select a má mít nějaké hodnoty, ale ty hodnoty nesedí s tím, co bylo odesláno (pamatujete?). Interně se skutečně vyhodí chyba `Please select a valid option.`, ale tu nikde nevykreslujeme, takže se zdá, že to prostě nefunguje. No fakt, vyzkoušejte si to:

```html
{form complicatedForm}
  <ul class="errors" n:if="$form->hasErrors()">
    <li n:foreach="$form->errors as $error">{$error}</li>
  </ul>
  {*{input selectNo1}*}
  {input send}
{/form}
```

Na to je třeba dávat velký bacha. Tento způsob je dostatečný snad pro všechno co je potřeba. Prakticky je však nevhodný...

# Třetí způsob - kodérka Silvie

Je velká škoda, že o tomto způsobu neví spousta lidí co potkávám. Kodér většinou vymyslí nádhernou šablonu s formulářem (s mnohem komplikovanějším než je tento):

```html
<form action="" method="post" id="myAwesomeFormId">
  <select name="selectNo1" id="myAwesomeSelectId">
    <option value="0" selected="true">Item 1</option>
    <option value="1">Item 2</option>
  </select>
  <input type="submit" name="send" value="Odeslat">
</form>
```

Teď se ale dostávám do problému, protože mám krásný formulář a měl bych jej zachovat. Mám však také jeho PHP definici a teď bych to potřeboval nějak naroubovat. Zde se stávají ty osudové chyby, kvůli kterým každý nadává - začne přepisování do druhého způsobu a hackování všeho co si kodér vymyslel. Když to dobře dopadne, tak bude formulář vypadat snad stejně jako vypadal původně. Snad...

To je ale zbytečné. Existuje lepší způsob pomocí `n:name` makra v Latte. Skutečně formulář pouze naroubujeme na ten v PHP a smažeme nepotřebné věci:

```html
<form n:name="complicatedForm" id="myAwesomeFormId">
  <select n:name="selectNo1" id="myAwesomeSelectId"/>
  <input n:name="send"/>
</form>
```

Formulář funguje pořád stejně, ale dokonce se i zjednodušil! No nicméně asi je z toho cítit, že pořád je na straně PHP dost práce (vlastně vešká potřebná zodpovědnost) a svoboda v šabloně je jen částečná. Zde přichází ke slovu poslední způsob.

# Čtvrtý způsob - bláznivý Joe

Všechno dříve zmíněné se mi nemusí líbit. Kašlu na nějaké definice v PHP, kašlu na automatické kontroly. Chci prostě vzít formulář od kodéra, odeslat ho a sám si ho zpracovat. Je to tak těžké pochopit?! Není milý Joe. Co to udělat takto - začněme s naroubouváním formuláře (ale jen fomuláře!):

```html
<form n:name=complicatedForm id="myAwesomeFormId">
  <select name="selectNo1" id="myAwesomeSelectId">
    <option value="0" selected="true">Item 1</option>
    <option value="1">Item 2</option>
  </select>
  <input type="submit" name="send" value="Odeslat">
</form>
```

Tím se zajistí, že se fomulář odešle na tu správnou adresu tou správnou metodou. Jinak jinak jsem do formuláře nezasáhl a je tedy úplně stejný, jako jej poslal kodér. Do puntíku. Veškerá data z formuláře jsou potom k dispozici zde:

```php
protected function createComponentComplicatedForm(): \Nette\Application\UI\Form
{
  $form = new \Nette\Application\UI\Form;
  $form->onSuccess[] = function (\Nette\Application\UI\Form $form) {
    dump($form->getHttpData()); //ZDE
  };
  return $form;
}
```

Co se stane, když záškodník odešle něco co by nemělo v selectu být (viz první příklad)? Přesně tak, prostě se to odešle a na serveru to přistane. Veškerá kontrola je tedy na programátorovi. K samotné hodnotě selectu je možno přistouput takto:

```php
$form->getHttpData($form::DATA_LINE, 'selectNo1')
```

To `DATA_LINE` je vhodné pro jednořádkové vstupy, protože se na pozadí provede následující ošetření:

```php
\Nette\Utils\Strings::trim(strtr($value, "\r
", '  '))
```

Pořád ale platí, že může přijít ze selectu nečekaná hodnota a nelze ničemu věřit. Jedná se však o naprosto svobodný způsob, který je možné kombinovat s předchozím. Pokud tedy máte _"neskutečně složitý formulář"_, který je _"už nakódovaný"_ a jeho definice _"má asi 500 řádek"_ a je to _"tak komplikované, že se v tom nikdo nevyzná"_ a bylo by _"lepší, kdybych si to mohl udělat sám"_, tak vězte, že to jde.

# Časté dotazy

- Použil jsi to někdy ve skutečnosti? Ano.
- Je tam nějaký problém o kterém bych měl vědět? Krom již zmiňovaného nevím o žádném. Je to prostě růčo fůčo...
- Proč to není v dokumentaci? [Je to tam.](https://doc.nette.org/en/2.4/forms#toc-manual-rendering)
- Proč není ten poslední způsob v dokumentaci? [Je to tam.](https://doc.nette.org/en/2.4/forms#toc-low-level-forms)
- Tak to tedy není v české verzi! I tam [to](https://doc.nette.org/cs/2.4/forms#toc-low-level-formulare) obojí [je](https://doc.nette.org/cs/2.4/forms#toc-manualni-vykreslovani)
- Tak to tam dřív nebylo. Je to tam [od verze 2.1](https://doc.nette.org/cs/2.1/forms), kdy se tato možnost objevila.
- I ve staré EN verzi? [Ano](https://doc.nette.org/en/2.1/forms)
- Šlo by to udělat i bez `getHttpData`? Ano, ale už v tom nevidím moc velký smysl.
- Proč? To už bych mohl rovnou pracovat s `$_POST`, ale tato metoda mě pěkně odstíní a [ošetří celou řadu potřebných věcí](https://api.nette.org/2.4/source-Forms.Form.php.html#385-403).
- Ale ve _{$frameworkName}_ se to dělá jinak a lépe! Použij tedy _{$frameworkName}_ nebo jeho podčást.

:)
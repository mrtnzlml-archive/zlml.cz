Občas je v Nette zapotřebí vyřešit dependent select box. Je to relativně málo častý požadavek a o to méně se o něm dá najít, když je to zrovna potřeba. V zásadě existují dvě řešení. Nudné - poctivé a pak zábavné - špinavé. Podívejme se na to, jak se dá takový dependent select box jednoduše vyřešit.

# Čisté řešení

[* 51d212f2-5aa9-44b9-9085-f6267e1974e9/vystrizek.png >]
Toto řešení ukazuje jak by se takový problém měl zhruba řešit. Myšlenka je velmi jednoduchá. Prvně potřebujeme data do závislého select boxu. Ty se normálně vyřeší prostřednistvím databáze, v našem případě postačí jednoduché pole.

```php
private $database = [
    [1 => '2', '4', '9'],
    [4 => '.', '∴', '…'],
    [5 => 'π', '€', '©'],
];
```

Následně je potřeba vytvořit samotný formulář:

```php
protected function createComponentForm($name) {
    $form = new UI\Form;
    $this[$name] = $form; // <- Zde je celý fígl
    
    $form->addSelect('one', 'One', ['Čísla', 'Tečky', 'Symboly'])->setDefaultValue(1);
    //dump($form['one']->value);
    
    $form->addSelect('two', 'Two', $this->database[$form['one']->value]);
    
    $form->addSubmit('send', 'Odeslat');
    $form->onSuccess[] = $this->success;
    return $form;
}
```

A k formuláři také šablonu:

```html
{form form}
    {input one, size => 3}
    {snippet two}
    	{input two, size => 3}
    {/snippet}
    {input send}
{/form}
```

Aby šlo použít snippet uvnitř formulářového makra, budeme muset udělat malý workaround:

```php
public function beforeRender() {
	parent::beforeRender();
	$this->template->_form = $this['form']; // form {snippet} workaround
}
```

Snippet je však možné zatím úplně vynechat, protože bude potřeba až při ajaxifikaci.

Celý fígl je v tom, že musíme zajistit, aby se hodnota druhého select boxu nastavovala podle hodnoty prvního. V tomto stavu je již možné formulář spustit. Po vybrání v prvním select boxu a odeslání formuláře se vybraná hodnota následně projeví v druhém select boxu. Celé kouzlo je pouze v té druhé řádce formulářové továrničky, který mi umožní přistoupit k hodnotám prvků. Je samozřejmě nesmysl odesílat celý formulář pro získání obsahu druhého select boxu, proto si napíšeme krátký javascriptový kód, který to za nás vyřeší (nette.ajax.js):

```javascript
$(function(){
	$.nette.init();
    
	$('select[name=one]').change(function () {
		$.nette.ajax({
			url: {link invalidate!},
			data: {
				'value': $('select[name=one]').val(),
			}
		});
	});
});
```

Jakmile se změní hodnota prvního select boxu, zavoláme si handler a předáme mu novou hodnotu. Tento handler bude mít za úkol nastavit hodnoty druhého select boxu a pouze tento prvek invalidovat:

```php
public function handleInvalidate($value) {
	$this['form']['two']->setItems($this->database[$value]);
	$this->redrawControl('two');
}
```

Tím je vlastně hotovo. Čistotu řešení ověříme tím, že si vyzkoušíme dump vybraných položek po odeslání formuláře:

```php
public function success(UI\Form $form, $vals) {
	dump($vals);
}
```

Čistota spočívá v tom, že požadované hodnoty skutečně získáme. To není úplně samozřejmé, protože v Nette existuje bezpečnostní obranný mechanismus, který zabraňuje odeslání hodnot v select boxu, které na začátku neobsahoval. Pokud něco takového uděláme třeba javascriptem, zíkáme `NULL`. Aby se toto nedělo, musíme takto relativně složitě vyřešit továrničku pro formulář.

# Špinavé řešení

Špinavé řešení se od toho čisté o moc neliší. Myšlenka je pořád stejná, tentokrát však stojíme před jiným úkolem. Vezměme si příklad, kdy nám zase až tolik nezáleží na tom, jaké získáme v select boxu hodnoty a chceme ho používat spíše jako text input, kdy nabídneme uživateli nějaký vstup. Typický příklad je text input pro URL adresu API, kdy po zadání kontaktujeme nějaké API a nabídneme v selectu vrácené hodnoty tak, aby je uživatel nemusel psát. Netvrdím, že by to nešlo vyřešit čistě, ale špinavé řešení je v tomto případě pohodlnější, rychlejší a mohu na něm ukázat i něco jiného. Tentokrát si vytvoříme úplně obyčejný formulář, tak jako již mnohokrát. Není potřeba žádného fíglu. Opět si připravíme šablonu pro formulář obdobně jako v předchozím případě. A obdobně doplníme nějaký ten javascript. Zde bych klidně mohl formulář upravit javascriptově, ale mě se hodí zavolat si (po napsání URL adresy) handler, v něm vyřešit vše potřebné a invalidovat část formuláře.

```javascript
var timer = null;
$('input[name=url]').live('keyup', function () {
	if (timer) {
		clearTimeout(timer);
	}
	timer = setTimeout(function () {
		timer = null;
		$.nette.ajax({
			url: {link checkErp!},
			type: 'POST',
			data: {
				"erpForm-url": $('input[name=url]').val()
			}
		});
	}, 250);
});
```

Handler nemá smysl uvádět. Jednoduše v něm vykonám nějakou logiku, vrátím data a invaliduji šablonu. Pozor na to, že předchozí javascriptová ukázka je tentokrát ze separátní komponenty.

Zbývá nám vyřešit pouze odeslání a zpracování formuláře. Jelikož jsem byl teď líný programátor a select v tomto případě beru spíše jako text input, nemohu získat hodnotu select boxu ve `$form->getValues()`, resp. v druhém parametru succeeded metody. Musím proto použít metodu `getHttpData`, která mi umožní získat jakákoliv data a zároveň mám jistotu, že jsou data ošetřena:

```php
public function erpFormSucceeded(UI\Form $form, Nette\Utils\ArrayHash $values) {
	try {
		$this->erpSystems->create([
			ErpSystems::COLUMN_URL => $values->url,
			ErpSystems::COLUMN_DATABASE => $form->getHttpData(UI\Form::DATA_LINE, 'database'), // <- náš select
			ErpSystems::COLUMN_USER => $values->username,
			ErpSystems::COLUMN_TOKEN => $values->password,
		]);
		//...
	} catch (\PDOException $exc) {
		//...
	}
	//...
}
```

V tomto špinavém řešení je však zapotřebí myslet na to, že v selectu může přijít naprosto cokoliv. To by však nikoho nemělo převapit. Ostatně i při vytváření formuláře stačí vytvořit select box prázdný, protože žádná vstupní data ani neexistují.
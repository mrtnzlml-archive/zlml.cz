---
timestamp: 1447611844000
title: Od indexu až po presenter
slug: od-indexu-az-po-presenter
---
Když jsem se učil pracovat s Nette Frameworkem, musel jsem v začátcích hodně přivírat oči a říkat si "prostě to tak je". Hodně věcí bylo zahaleno do tmy. Teď už to tak naštěstí není, ale stále se stává, že mi někdo napíše a děkuje za poodhalení a vysvětlení toho, jak to funguje na pozadí (za což zase děkuji já). Pokusím se tedy pokračovat a vrátím se na úplný začátek - do `index.php` a poodhalím, jak probíhá start takové běžné aplikace. A jako vždy - co nejjednodušeji.

Zodpovím (nebo alespoň nastíním odpovědi na) následující otázky:
- proč redirect vyvolává AbortException
- jak napsat vlastní NanoPresenter
- proč má Nette dva request objekty
- kde se bere životní cyklus presenteru

# Start aplikace

Nedávno jsem dostal v práci na starost implementovat Nette do jednoho legacy projektu. Už jsem tu o tom [psal](navrhovy-vzor-legacy-code). Byl to nesmírně vyčerpávající úkol, ale už mám hotovo a jsem ve fázi nekonečného refaktoringu. Jednou z prvních věcí, které bylo nutné vyřešit byl start aplikace z jednoho místa. Toto naštěstí řeší [web-project](https://github.com/nette/web-project) (nebo [sandbox](https://github.com/nette/sandbox) chcete-li) už v základu takto (`.htaccess`):

```
<IfModule mod_rewrite.c>
	RewriteEngine On

	RewriteCond %{REQUEST_FILENAME} !-f
	RewriteCond %{REQUEST_FILENAME} !-d
	RewriteRule !\.(pdf|js|ico|gif|jpg|png|css|rar|zip|tar\.gz|map)$ index.php [L]
</IfModule>
```

Čímž se velmi rychle dostáváme k prvnímu bodu, kterým je `index.php`:

```php
/** @var \Nette\DI\Container $container */
$container = require __DIR__ . '/../app/bootstrap.php';
/** @var \Nette\Application\Application $application */
$application = $container->getByType(\Nette\Application\Application::class);
$application->run();
```

V tomto souboru vše začíná a také končí. Totiž zavolá se ještě minimálně `\Tracy\Debugger::shutdownHandler`, `\Nette\Http\Session::clean` a `\Nette\Http\Response::__destruct`, ale zůstaňme u toho, že zde vše začíná a také končí. O co v indexu vlastně jde? Hned přeskočím první řádku, ačkoliv se jedná o nezanedbatelnou část. Z bootrapu získáme hotovou instanci [DIC](https://doc.nette.org/cs/2.3/dependency-injection), resp. přímého potomka. Následuje vytažení [Application](https://api.nette.org/2.3.7/Nette.Application.Application.html) a naškytnutí aplikace pomocí metody `run`. To je předpokládám všem jasné, proto jsem to vzal letem světem. Cílem tohoto článku je však popsání právě `run` metod.

# Run, run!

Metoda `\Nette\Application\Application::run` vypadá přesně takto:

```php
public function run()
{
    try {
        $this->onStartup($this);
        $this->processRequest($this->createInitialRequest());
        $this->onShutdown($this);
    } catch (\Exception $e) {
        $this->onError($this, $e);
        if ($this->catchExceptions && $this->errorPresenter) {
            try {
                $this->processException($e);
                $this->onShutdown($this, $e);
                return;
            } catch (\Exception $e) {
                $this->onError($this, $e);
            }
        }
        $this->onShutdown($this, $e);
        throw $e;
    }
}
```

Pro přehlednost to ještě zjednoduším a vyhodím vše, co pro samotné spuštění aplikace není nezbytně nutné:

```php
public function run()
{
    try {
        $this->processRequest($this->createInitialRequest());
    } catch (\Exception $e) {
        if ($this->catchExceptions && $this->errorPresenter) {
            $this->processException($e);
            return;
        }
        throw $e;
    }
}
```

Moc toho opět nezbylo. Vlastně se zde dějí jen tři věci. Prvně [createInitialRequest](https://api.nette.org/2.3.7/source-Application.Application.php.html#102-124). Tato metoda vrátí (jak už název napovídá) tzv. aplikační request. To je objekt, který pak putuje celou aplikací a nese si informaci o tom co vlastně uživatel chce. Vzpomínáte si na článek o [dynamickém routování URL adres](dynamicke-routovani-url-adres)? Tam jsem ukazoval, jak se HTTP request změní právě na aplikační a zpět. Celá sranda se tedy odehrává někde v RouterFactory (zatím to platí, do budoucna ale [nebude](https://github.com/nette/routing/commit/e802a85e96f5814ddf1a16ea1517398eb560bab6)). Samotný HTTP request se pak dostane do `Application` díky DI. Už v `createInitialRequest` je tedy jasné, jestli je možné požadavek přeložit, nebo 404. Pokud 404, tak konec, resp. `processException` pokud je to v configu zapnuté (což jinými slovy znamená forward na error presenter):

```neon
application:
    catchExceptions: yes
```

Pokud se však povede získat aplikační request, nastupuje funkce `processRequest`. Mrkněte na [implementaci](https://api.nette.org/2.3.7/source-Application.Application.php.html#127-150). Opět se nejedná o nic složitého. Jde zde vlastně jen o jedinou věc. Získat presenter a zavolat nad ním `run`. Proč `run`? Presenter totiž není potomek `\Nette\Application\UI\Presenter` jak si pamatují skoro všichni, ale spíše objekt, který implementuje interface `\Nette\Application\IPresenter` jehož jedinou metodou je právě `run` do které se jako jediný parametr předává již zmíněný aplikační request. Zároveň musí tato metoda vracet `\Nette\Application\IResponse`.

Z toho plyne, že pokud potřebujeme velmi jednoduchý presenter (který skoro nic neumí, ale je rychlý), stačí si implementovat `IPresenter` rozhraní a hotovo:

```php
class NanoPresenter extends Nette\Object implements Nette\Application\IPresenter
{

	public function run(Nette\Application\Request $appRequest)
	{
		return new Nette\Application\Responses\TextResponse('It works!');
	}

}
```

V běžném presenteru se toho však děje samozřejmě mnohem více. Právě v metodě `run` se schovává celý dobře známý životní cyklus presenteru. Než se však dostanu k reálnému příkladu, začnu na tomto jednoduchém. `NanoPresenter` vrací pouze `TextResponse` s obyčejným textem. Tato odpověď je předána zpět do `processRequest` a následně je hned zavoláno `\Nette\Application\IResponse::send` což v tomto konkrétním případě vyústí v obyčejné echo. Co se však děje v běžném presenteru?

# Vykreslení šablony

Presenter, který dědí od `\Nette\Application\UI\Presenter` dělá téměř to samé. Jediný rozdíl je v tom, že presenter vlastně vykreslitelná komponenta, takže si vezme šablonu a předá ji stejně jako v předchozím případě do `TextResponse`. Zde je oproti mému `NanoPresenter` příkladu malý implementační rozdíl, ale ve výsledku presenter tak jako tak `TextResponse` vrátí zpět `Application` objektu do `processRequest` metody. Dále se opět zavolá `\Nette\Application\Responses\TextResponse::send`, tentokrát však nedojde k obyčejnému echu, ale spustí se renderování předané šablony (`\Nette\Application\UI\ITemplate`). Většinou to tedy propadne na Latte, ale to samozřejmě není podmínkou.

```php
public function send(Nette\Http\IRequest $httpRequest, Nette\Http\IResponse $httpResponse)
{
    if ($this->source instanceof Nette\Application\UI\ITemplate) {
        $this->source->render();
    } else {
        echo $this->source;
    }
}
```

Z předchozího textu by mělo být zřejmé, kudy požadavek putuje. Když si prohlédnete detailněji to co jsem zde popsal, mělo by být jasné, že se interně používá `\Nette\Application\AbortException` a proto je nebezpečné v presenterech chytat bezmyšlenkovitě všechny výjimky. Nikdo pak nechce řešit "proč to Nette zase nepřesměrovává".

Tento text by měl překlenout tu temnou propast mezi `index.php` a akcí v presenteru. Příště už se snad vrhnu na něco zajímavějšího... :)
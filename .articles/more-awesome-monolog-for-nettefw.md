Nedávno mi přišel požadavek na vytvoření takového jednoduchého způsobu, jak logovat uživatelské akce - konkrétně zatím jen přihlášení uživatele (do databáze). Mohl jsem to udělat jednoduše a prostě to někam do kódu nahákovat. A nebo jsem to mohl udělat tak, jak jsem to také nakonec udělal - složitě. Samozřejmě je třeba dopředu dostatečně promyslet, jestli to za tu práci stojí, ale měl jsem dostatek argumentů proto, že ano.

Jedna z nejdůležitějších myšlenek byla, že časem bude pravděpodobně potřeba logovat do databáze i další akce, než je jen přihlašování. Druhým velmi silným faktorem (který ovlivnil celé řešení) bylo, že používáme [Monolog](https://github.com/Seldaek/monolog). Konkrétně Kdyby\Monolog rozšíření.

Kdyby Monolog
=============
Znáte tuto knihovnu ([GitHub](https://github.com/Kdyby/Monolog))? Věřím, že ano. Kolem Kdyby knihoven byl velký hype. Velká síla této knihovny je v tom, že se dokáže velmi jednoduše napojit na Tracy a to takovým způsobem, že můžete používat Monolog bez BC breaku (tedy pomocí `Tracy\Debugger::log()`). Větším přiblížením k Monologu jako takovému, je pak používání služby `Kdyby\Monolog\Logger` resp. `Monolog\Logger` jako závislosti v konstruktoru.

Kdyby logger pak obsahuje tato nastavení (ne všechny jsou v dokumentaci):

```neon
monolog:
	handlers: []
    processors: []
    name: app
    hookToTracy: yes
    tracyBaseUrl: NULL
    usePriorityProcessor: yes
    registerFallback: yes
```

Pro programátora je asi nejzajímavější možnost nastavit si vlastní [handlery](https://github.com/Seldaek/monolog/blob/master/doc/02-handlers-formatters-processors.md#handlers) a [procesory](https://github.com/Seldaek/monolog/blob/master/doc/02-handlers-formatters-processors.md#processors). Pozornějšímu oku však neunikne, že toto nastavení funguje globálně pro celou aplikaci. Jenže občas se hodí mít více loggerů a každý mít nastavený jinak. Nevím, jestli jsem náhodou Kdyby\Monolog nepochopil špatně, ale jedinou šancí jak toho dosáhnout, je nenastavoval logger v configu, vytvořit si vlastní `Logger` objekty a ty si nastavit (`pushHandler`, `pushProcessor` a `setFormatter` u handlerů). A to je naprd.

Adeira Monolog
================
Mít více vlastních loggerů už se mi párkrát hodilo. A právě i zde by bylo super mít objekt, který mi umožní jednoduše něco zalogovat do databáze. Zároveň je však super propojení s Tracy, které má tak skvělě vyřešené Kdyby\Monolog. Jak z toho vybruslit? Vlastním Composer balíčkem. Než se dostanu k samotné instalaci a nastavení, ukážu zde, jak jsem využil vlastní logger. Pořád však mějte na paměti, že si kdykoliv musím být schopen sáhnout na původní `Kdyby\Monolog\Logger` a vesele logovat podle globálního nastavení!

Prvně jsem si vytvořil vlastní logger:

```php
<?php

namespace App\Audit\Monolog\Loggers;

class UsersAuditLogger extends \Monolog\Logger
{

}
```

Přesně, víc toho není potřeba. Tento objekt vlastně slouží jen k tomu, abych měl co předávat v konstruktoru jako závislost. A zároveň je to ta nejdivnější část - teď už to bude jen lepší. Teď by bylo fajn mít vlastní handler, který mu později přiřadíme a který se bude starat o to ukládání do databáze. Šlo by udělat to, že by handler skutečně ukládal rovnou do databáze, ale použiju zde malý fígl:

```php
<?php

namespace App\Audit\Monolog\Handler;

class UsersAuditDatabaseHandler extends \Monolog\Handler\AbstractProcessingHandler
{

	/** @var \App\Audit\EntitiesQueue */
    private $entitiesQueue;

	public function __construct(\App\Audit\EntitiesQueue $entitiesQueue)
	{
		$this->entitiesQueue = $entitiesQueue;
		parent::__construct(\Monolog\Logger::DEBUG, TRUE); //TRUE - bubble
	}

	protected function write(array $record)
    {
        $auditRecord = new \App\Audit\AuditRecord($record['level'], $record['message'], $record['datetime']);
        $this->entitiesQueue->add($auditRecord);
    }

}
```

Je to jen zjednodušený příklad. Důležité ale je, že jsem zde zatím nic neuložil, ale pouze to zařadil do jakési fronty na entity. To proto, že logů může být více a chci si je napřed všechny posbírat a následně je v jedné transakci poslat do databáze. Objekt `AuditRecord` je obyčejná Doctrine entita. `EntitiesQueue` je jednoduchý objekt, který tyto entity sbírá a zároveň naslouchá na `Application::onShutdown`:

```php
<?php

namespace App\Audit;

class EntitiesQueue implements \Kdyby\Events\Subscriber
{
	//závislosti, prázdné pole, metoda 'add' atd. ...

	public function getSubscribedEvents()
    {
        return ['Nette\Application\Application::onShutdown' => 'onShutdown'];
    }

    public function onShutdown()
    {
        foreach ($this->entities as $entity) {
            $this->em->persist($entity);
        }
        $this->em->flush();
        $this->entities = [];
    }

}
```

Veškeré logy se tedy uloží až v okamžiku, kdy aplikace ukončuje svůj životní běh (podobný princip se používá i při práci s RabbitMQ). Použití je pak triviální. V jiném listeneru naslouchám na událost `Nette\Security\User::onLoggedIn` a v tomto okamžiku zaloguji zprávu. Ten vlastní `UsersAuditLogger` je zde jako závislost.

```php
$this->usersAuditLogger->addInfo("User with login '$login' logged into administration.");
```

Instalace a nastavení balíčku
=============================
Ok, cool - jak to použiju? Úplně jednoduše:

```
composer require adeira/monolog
```

Následně zaregistrujeme rozšíření do DIC:

```neon
extensions:
    monolog: Adeira\Monolog\DI\MonologExtension
```

A dále už to znáte. Následuje krátká ukázka toho, co se s tím dá dělat za švíky. Tento balíček sám od sebe registruje spoustu formátovačů, procesorů a jeden handler z Monologu ([viz readme](https://github.com/adeira/monolog/blob/master/readme.md)). Ty je pak možné jednoduše používat pouze podle jejich názvu. Zároveň je zachována téměř veškerá funkcionalita Kdyby\Monologu (ano, můj balíček toto rozšíření rozšiřuje, protože napojení na Tracy je fakt super). Mohu si pak nastavit loggery třeba takto:

```neon
monolog:
	hookToTracy: yes
	registerFallback: yes
	usePriorityProcessor: yes

	processors:
		web: Monolog\Processor\WebProcessor(NULL, [
				ip: REMOTE_ADDR,
				userAgent: HTTP_USER_AGENT,
			])

	handlers:
		database:
			class: App\Audit\Monolog\Handler\UsersAuditDatabaseHandler
			processors: [web]
		slack:
			class: Adeira\Monolog\Handler\SlackHandler(
				productionMode: %productionMode%,
				token: '.....',
				channel: exceptions,
				username: 'Monolog',
				useAttachment: no,
				iconEmoji: poop,
				level: Monolog\Logger::CRITICAL,
				bubble: yes,
				useShortAttachment: no,
				includeContextAndExtra: yes,
			)

	loggers:
		global: #global logger from Kdyby (\Kdyby\Monolog\Logger)
			handlers: [slack]
			processors: [git, web]
		- class: App\Audit\Monolog\Loggers\UsersAuditLogger
		  handlers: [database]
```

A teď slovně. V sekci `processors` měním chování `web` procesoru. Ten je sice již zaregistrovaný, ale nelíbí se mi jeho chování. Vlastní konfigurace má přednost. Dále přidávám dva nové handlery (`database` a `slack`). Databázový handler používá procesor `web` na které jsem se odkázal jen pomocí názvu. O propojení se již postará knihovna sama. V balíčku je připravený handler `SlackHandler`, který navíc oproti původnímu z Monologu chová tak, že zprávičky posílá jen jednou za 15 minut. To může být užitečné... :-)

No a konečně samotné loggery. Logger z Tracy lze nastavit pomocí speciální sekce `global`. Zde si nastavuji slack handler a 2 procesory. Zároveň přidávám ještě vlastní logger (registruje se stejně jako v sekci `services`) a nastavuji mu databázové handler. A o to vlastně celou dobu šlo.

Pretty cool, hm? [PR a hvězdičky prosím sem](https://github.com/adeira/monolog)... :)

Dodatečné odkazy na čtení:
- [Logging with Monolog](http://symfony.com/doc/current/logging.html)
- [How to Log Messages to different Files](http://symfony.com/doc/current/logging/channels_handlers.html)
- [Configure multiple loggers and handlers](https://github.com/theorchard/monolog-cascade)

[Follow me on Twitter and stay tuned](https://twitter.com/mrtnzlml)...
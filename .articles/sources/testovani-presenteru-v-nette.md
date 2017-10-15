---
timestamp: 1375043798000
title: Testování presenterů v Nette
slug: testovani-presenteru-v-nette
---
Tak toto je přesně to téma o kterém se naustále mluví, ale tím to z velké části končí.
Nemá smysl zabývat se tím, jestli testovat, nebo ne. Na to už si každý přijde sám.
V následujících odstavcích bych rád předvedl myšlenku jak si
ušetřit pár řádek kódu při testech (\Nette\Tester).

# Nezbytná teorie


Pro testování presenterů je zapotřebí získat továrnu na presentery PresenterFactory
a následně daný presenter vyrobit. Například takto:

```php
$presenterFactory = $this->container->getByType('Nette\Application\IPresenterFactory');
$this->presenter = $presenterFactory->createPresenter('Front:Homepage');
```

K tomu je zapotřebí \Nette\DI\Container, který získáme například v konstruktoru, nebo
pomocí inject anotace.

Následně je třeba vytvořit požadavek, ten spustit a testovat výslednou odpověď:

```php
$request = new \Nette\Application\Request($this->presName, $method, $params, $post);
$response = $this->presenter->run($request);
```

Právě nad vrácenou odpovědí lze spustit testovací sadu, která bude testovat, 
zda byla například získána textová odpověď a tedy jestli se jedná o šablonu:

```php
\Tester\Assert::true($response instanceof \Nette\Application\Responses\TextResponse);
\Tester\Assert::true($response->getSource() instanceof \Nette\Templating\ITemplate);
```

Je také vhodné otestovat samotný HTML kód. Již mě to párkrát upozornilo na
nevalidní kód, což se může stát, pokud se šablona skládá z hodně include částí.
Nevalidní ve smyslu například dvojité HTML ukončovací značky:

```php
$html = (string)$response->getSource();
$dom = \Tester\DomQuery::fromHtml($html);
\Tester\Assert::true($dom->has('title'));
```

# Psaní, psaní, psaní...


Předchozí teorie je zapotřebí opakovat pro každý presenter. Už jen proto, že je třeba
vytvořit pokaždé nový požadavek. Nicméně je jasné, že to po otestování FrontModule
začne být lehce kopírovací nuda.

Je tedy vhodné vytvořit si třídu, která ušetří spoustu řádek.
Můj první návrh vypadá přibližně takto:

```php
<?php

namespace Test;

class Presenter extends \Nette\Object {

        private $container;
        private $presenter;
        private $presName;

        public function __construct(\Nette\DI\Container $container) {
                $this->container = $container;
        }

        /**
         * @param $presName string Fully qualified presenter name.
         */
        public function init($presName) {
                $presenterFactory = $this->container->getByType('Nette\Application\IPresenterFactory');
                $this->presenter = $presenterFactory->createPresenter($presName);
                $this->presenter->autoCanonicalize = FALSE;
                $this->presName = $presName;
        }

        public function test($action, $method = 'GET', $params = array(), $post = array()) {
                $params['action'] = $action;
                $request = new \Nette\Application\Request($this->presName, $method, $params, $post);
                $response = $this->presenter->run($request);
                return $response;
        }

        public function testAction($action, $method = 'GET', $params = array(), $post = array()) {
                $response = $this->test($action, $method, $params, $post);

                \Tester\Assert::true($response instanceof \Nette\Application\Responses\TextResponse);
                \Tester\Assert::true($response->getSource() instanceof \Nette\Templating\ITemplate);

                $html = (string)$response->getSource();
                $dom = \Tester\DomQuery::fromHtml($html);
                \Tester\Assert::true($dom->has('title'));

                return $response;
        }

        public function testForm($action, $method = 'POST', $post = array()) {
                $response = $this->test($action, $method, $post);

                \Tester\Assert::true($response instanceof \Nette\Application\Responses\RedirectResponse);

                return $response;
        }

}
```

Testování samotných presenterů je pak již otázkou několika málo řádek:

```php
<?php

namespace Test;

$container = require __DIR__ . '/../bootstrap.php';

class HomepagePresenterTest extends \Tester\TestCase {

        public function __construct(\Nette\DI\Container $container) {
                $this->tester = new \Test\Presenter($container);
        }

        public function setUp() {
                $this->tester->init('Front:Homepage');
        }

        public function testRenderDefault() {
                $this->tester->testAction('default');
        }

}

id(new HomepagePresenterTest($container))->run();
```

Takto chápu testování presenterů v Nette já. Dále budu směrovat testy tak, abych nemusel psát téměř nic
a měl jsem otestováno téměř všechno. Myslím si, že toto je jediná cesta jak se přinutit k testování.
Nelze se již vymlouvat na to, že je to spousta psaní navíc. Není.
---
timestamp: 1375087004000
title: Třída pro připojení k FIO API
slug: trida-pro-pripojeni-k-fio-api
---
Další užitečný úryvek, který je škoda nechat ležet v Git repozitářích.
A opět uzpůsobený pro používání s Nette FW.

Nedávno jsem psal o tom, jak používat CLI router v Nette (http://zlml.cz/nette-2-1-dev-clirouter).
Právě pomocí tohoto routeru je vhodné kontrolovat bankovní výpisy - například pomocí cronu:

```php
<?php

namespace App\CliModule;

use Nette;
use Nette\Diagnostics\Debugger;

/**
 * Class CliPresenter
 * @package App\CliModule
 */
class CliPresenter extends BasePresenter {

        /** @var \Fio @inject */
        public $fio;
        ...

        public function actionCron() {
                $this->checkFio(); // FIO vs. nezaplacené objednávky
                ...
                $this->terminate();
        }

        /**
         * Zkontroluje bankovní účet, porovná s databází a zaplacené objednávky změní na status PAID.
         */
        private function checkFio() {
                try {
                        $transactions = $this->fio->transactions();
                        $unpaid = $this->orders->selectUnpaidOrders(); //získání nezaplacených objednávek
                        //array_intersect() - zde samotné zpracování
                        ...
                } catch (\Exception $exc) {
                        Debugger::log($exc->getMessage() . ' FILE: ' . $exc->getFile() . ' on line: ' . $exc->getLine(), Debugger::WARNING);
                        echo $exc->getMessage() . EOL;
                }
        }
}
```

K tomu se hodí právě následující třída:

```php
<?php

/**
 * Class Fio
 */
class Fio extends \Nette\Object {

        private $token;
        private $rest_url = 'https://www.fio.cz/ib_api/rest/';

        /**
         * @param string $token SECURE
         */
        public function __construct($token) {
                $this->token = $token;
        }

        /**
         * Pohyby na účtu za určené období.
         * JSON only!
         * @param string $from
         * @param string $to
         * @return array|mixed
         */
        public function transactions($from = '-1 month', $to = 'now') {
                $from = \Nette\DateTime::from($from)->format('Y-m-d');
                $to = \Nette\DateTime::from($to)->format('Y-m-d');
                $url = $this->rest_url . 'periods/' . $this->token . '/' . $from . '/' . $to . '/transactions.json';
                return $this->parseJSON($this->download($url));
        }

        /**
         * Oficiální výpisy pohybů z účtu.
         * JSON only!
         * @param $id
         * @param null $year
         * @return array|mixed
         */
        public function transactionsByID($id, $year = NULL) {
                if ($year === NULL) {
                        $year = date('Y');
                }
                $url = $this->rest_url . 'by-id/' . $this->token . '/' . $year . '/' . $id . '/transactions.json';
                return $this->parseJSON($this->download($url));
        }

        /**
         * Pohyby na účtu od posledního stažení.
         * JSON only!
         * @return array|mixed
         */
        public function transactionsLast() {
                $url = $this->rest_url . 'last/' . $this->token . '/transactions.json';
                return $this->parseJSON($this->download($url));
        }

        /**
         * @param $url
         * @return mixed
         * @throws \Exception
         */
        private function download($url) {
                if (!extension_loaded('curl')) {
                        throw new \Exception('Curl extension, does\'t loaded.');
                }
                $curl = curl_init();
                curl_setopt($curl, CURLOPT_URL, $url);
                curl_setopt($curl, CURLOPT_HEADER, FALSE);
                curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
                curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
                curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
                $result = curl_exec($curl);
                return $result;
                //return file_get_contents($url); //ALTERNATIVE
        }

        /**
         * @param $data
         * @return array|mixed
         */
        private function parseJSON($data) {
                $json = json_decode($data);
                if($json === NULL) {
                        //Moc ryhlé požadavky na Fio API
                        throw new \Exception('Fio API overheated. Please wait...');
                        //Když se posílá stále moc požadavků, tak se to z Exception nikdy nevyhrabe. Musí se opravdu počkat.
                }
                if(!$json->accountStatement->transactionList) {
                        return $json; // There are no transactions (header only)
                }
                $payments = array();
                foreach ($json->accountStatement->transactionList->transaction as $row) {
                        $out = array();
                        foreach ($row as $column) {
                                if ($column) {
                                        $out[$column->id] = $column->value; //v $column->name je název položky
                                        /*
                                         * 0  - Datum
                                         * 1  - Částka (!)
                                         * 5  - Variabilní symbol (!)
                                         * 14 - Měna (!)
                                         * Hodnoty (!) se musí použít ke kontrole správnosti...
                                         */
                                }
                        }
                        array_push($payments, $out);
                }
                return $payments;
        }

}
```

S tím, že je zapotřebí předat FIO klíč z neonu. FIO třída se automaticky injectuje, tzn. že i konstruktor
této třídy bude doplněn automaticky. Je jen zapotřebí přidat do neonu onu konfiguraci:

```neon
parameters:
	fio_token: '' #token pro přístup do FIO banky
    
...

services:
	- Fresh\Fio(token: %fio_token%)
    
...
```

Bylo by vhodné upozornit na fakt, že se jedná pouze o read-only přístup, tzn. že neexistují žádné funkce
pro zápis (ačkoliv existuje něco jako datumová zarážka). Díky tomu je možné použít takovéto nízkoúrovňové
zabezpečení pomocí jednoho tokenu.
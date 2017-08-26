---
id: 52dfa065-130b-4c37-81aa-289bd6ceee9e
timestamp: 1392936502000
title: Kde se berou spamy?
slug: kde-se-berou-spamy
---
<div class="alert alert-info">Tento článek navazuje na článek [Stáhněte si zdarma 897457 emailových adres](stahnete-si-zdarma-897457-emailovych-adres) z ledna tohoto roku. Přečtěte si jej prosím, ať víte o co jde.</div>

Rád bych tímto všechny čtenáře poprosil o pomoc. Ačkoliv na internetu vystupuji veřejně a nemám s tím problém, jsem velmi háklivý na to, když někdo neoprávněně zneužívá mé osobní informace. Někteří přijmou moji žádost a problém odstraní - viz nedávno zrušená kopie tohoto blogu. Někteří však dělají všechno proto, abych pokračoval v rituálním podpalování válečné sekery a proto jsem neskončil tam kde jsem v minulém článku přestal psát.

# Trnitá cesta plná překážek

Je již známá věc, že jistý web má nemalou zásluhu na šíření reklamních emailů křížem krážem. Vzhledem k tomu, že takové email dorazil i ke mě a vzhledem k tomu, že upoutal moji pozornost, začal jsem zbrojit. Při prvních pokusech jsem oťukával jejich systém abych zjistil o co jde. Zde jsem mimo jiné udělal mylný myšlenkový pochod a vše jsem svedl na někoho kdo s tím nemá nic společného. Každopádně po několika minutách jsem měl celkem jasno a udělal jsem velkou chybu. Popsal jsem bezpečnostní chybu reklamního systému na G+, na což někdo velmi rychle zareagoval a já jsem dostal na tento reklamní server ban. Doufám, že to byla jen náhoda...

Toto řešení mi přišlo poněkud směšné, a tak jsem do tohoto systému přes IP anonymizér šťoural stále více. V tuto chvíli jsem to prakticky zabalil, protože chyba byla tak nějak opravena. Systém již při odhlašování nezobrazoval emailovou adresu, takže již nešlo použít něco jako:

```php
preg_match('#<b>(.+)</b>#', file_get_contents("http://www.m-letter.eu/odh.html?c=$i&s=53&q=51"), $match);
```

Na necelý měsíc jsem to pustil z hlavy až včera jsem si na tento web opět vzpomněl. Také jsem byl již na jiné IP adrese což se mi stává celkem často, takže jsem opět vyzkoušel to co před měsícem a světe div se, bezpečností chyba opět funguje. To mě rozproudilo ještě víc, takže jsem opět usedl k editoru a začal jsem psát program, abych si ověřil, že jsem nekecal:

```php
<?php
try {
    $db = new PDO('mysql:dbname=emails;host=127.0.0.1', 'root', '');
    $stmt = $db->prepare("INSERT INTO emails (url_id, email) VALUES (:url_id, :email)
							ON DUPLICATE KEY UPDATE url_id= :url_id, email= :email");
	$stmt->bindParam(':url_id', $url_id);
	$stmt->bindParam(':email', $email);

	//1300486 - 2197943
	for ($i=2197943; $i > 1300486; $i--) {
		preg_match('#<b>(.+)</b>#', file_get_contents("http://www.m-letter.eu/odh.html?c=$i&s=53&q=51"), $match);
		if (preg_match("#@#", $match[1])) {
			$url_id = $i;
		    $email = $match[1];
		    echo $i . ': ' . $email . "
";
			$stmt->execute();
		}
	}

} catch (PDOException $e) {
    echo 'Connection failed: ' . $e->getMessage();
}
```

Nejsem žádný extra programátor, ale na takovou věc ani být nemusím. Je asi zřejmé, že by tento program trval poměrně dlouhou dobu. Nemá však smysl řešit nějaké paralelní zpracování, když se o to server při vhodně zvoleném programu postará sám. Napsal jsem tedy celkem tři velmi podobné programy. Jeden bral URL adresy od nízkého čísla, druhý od vysokého a třetí na obě strany od středu číselného intervalu. Právě je 17:00, dávám si něco k jídlu a vyrážím do města na hokej...

# Jak to bylo dál?

Po tom co hokej nedopadl moc dobře, strávil jsem nějaký čas u piva a vrátil jsem se domů. Bylo pozdě a program pořád běžel. Šel jsem spát a ráno do školy. Program stále běžel. V tuto chvíli již 16 hodin. Odhadem až někdy po 20ti hodinách dolování emailových adres z tohoto serveru jsem opět dostal IP ban. V tu chvíli jsem měl však získáno více než čtvrt milionu unikátních emailových adres. Ono to funguje! **Zde bych měl říct, že jsem tyto emailové adresy nedoloval kvůli nějakému zneužití, maximálně z nich udělám nějakou statistiku**... (-: Chtěl jsem také napsat na email který mají vystavený na webu, to bohužel nefungovalo, protože tento email byl pravděpodobně zrušen.

V tuto chvíli adresy stále zpracovávám, protože celý systém psal někdo moc "šikovný" a tak občas systém vrátil místo emailu *Invalid key!* a jindy zase mix náhodných speciálních znaků s čímž jsem nepočítal. Také já jsem byl šikovný, takže jsem sice v programu počítal s unique klíčem, ale v DB jsem ho neudělal a v reklamním systému je jich cca 10% duplicitních.

Žádám tedy o pomoc. Víte komu patří emailová adresa `b2bdm@email.cz`? Komu patří `m-letter.eu`? Kdo zneužívá tak obrovské množství emailových adres? A kde je vůbec bere? Skutečně mě to zajímá, protože takto je to těžko představitelné, ale několik set tisíc adres je skutečně obrovské množství a já stále nevím, kde jsem se tam vzal...
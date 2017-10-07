---
id: e0783b28-8a1a-400c-b91b-547b4c372fbe
timestamp: 1387710504000
title: Veřejná distribuce klíčů
slug: verejna-distribuce-klicu
---
<blockquote>
  Inspirací a zdrojem informací pro tento článek byla kniha <strong>Simona Singha</strong> - Kniha kódu a šifer.
  <small>Utajování od starého Egypta po kvantovou kryptografii</small>
</blockquote>

Tímto článkem bych rád navázal na článek o asymetrickém šifrování http://zlml.cz/asymetricka-sifra-s-verejnym-klicem a vyřešil tak několik restů. Zejména potom onu osudnou veřejnou distribuci klíčů o které jsem sice již dříve psal, ale článek již není k dispozici. Proto tento text budu brát jako revizi původního. Také bych na začátek chtěl říct, že tento problém je již dávno vyřešen a proto bude následující text ohlédnutím za vznikem této myšlenky s tím, že je však použití stále aktuální a reálně se používá (např.: http://nodejs.org/api/crypto.html#crypto_class_diffiehellman).

# Distribuce klíčů? Vždyť je to tak snadné...

Pokud si chtějí dvě osoby vyměnit zašifrované zprávy, je jasné, že musejí znát i klíče, které jim umožní tyto zprávy dešifrovat. Jenže jak si vyměnit tyto klíče? Mohou se tyto osoby někdy potkat a klíč si povědět. To však není vždy možné. Navíc klíče je dobé frekventovaně měnit, takže je v dnešní době toto řešení naprosto nesmyslné.

<blockquote>
  Dříve, než dva lidé mohou sdílet tajemství, musí již jedno tajemství sdílet.
  <small>Dříve než dva lidé mohou sdílet šifrovanou zprávu, musí již sdílet klíč.</small>
</blockquote>

Ačkoliv je osobní výměna bezpečná, jedná se tedy o metodu nereálnou a je třeba navrhnout jiné postupy. Co třeba najmou kurýra? Je to sice méně bezpečné, ale eliminují se některé předchozí problémy. Bohužel kurýr je až příliš nebezpečný způsob, protože pak lze klíč rovnou nějakým kanálem poslat a dostáváme se opět na začátek. Je tedy vůbec možné si vyměnit klíč bez nutnosti potkat se? Je tedy vůbec nutné si klíč vyměnit?

# Možná to jde i jinak

Existuje skvělá hádanka, která na první pohled daný problém řeší. Představte si poštovní službu, která však všechny zásilky otevírá a čte si je. Nicméně Alice potřebuje poslat tajný balík Bobovi. Lze využít tuto poštu tak, aniž by balík otevřela?

Přistupím rovnou k řešení, které je opravdu jednoduché. Alice pošle balík (schránku), který opatří vlastním zámkem a klíč od tohoto zámku si ponechá. V tom případě není pošta schopna balík otevřít. Bohužel ani Bob balík neumí otevřít, protože nemá k dispozici správný klíč. Proto Bob vezme vlastní zámek a schránku zamkne ještě vlastním zámkem. Klíč si opět ponechá. To může vyznít zvláštně, ale hned to začne být jasné. Bob balík opět odešle, Alice sundá vlastní zámek (protože od něj má klíč) a balík pošle opět Bobovi. Nyní je na balíku pouze Bobovo zámek a ten ho může jednoduše odemknout. Zdá se tedy, že lze cokoliv poslat zabezpečeně a výměna klíčů není potřeba! Toto je nesmírně důležitá myšlenka.

Má to však háček. Ačkoliv se zdá být předchozí problém naprosto zřejmý a funkční, po převedení do světa kódů a šifer, celá myšlenka padá. Důvod je jednoduchý. Bylo velmi snadné na schránku umístit zámek **A**, poté zámek **B**, poté odstranit zámek **A** a nakonec odstranit zámek **B**. Takže posloupnost  šifrování byla +A => +B => -A => -B. Použijete-li však tento postup pomocí doposud známých šifer, zjistíte, že záleží na pořadí šifrování, resp. dešifrování. Zkrátka nelze toto pořadí zaměnit, jinak je výsledek zamíchaný a nepoužitelný.

# Tak to je problém...

Ačkoliv byla myšlenka posílání balíku téměř ideální, ve světě šifer již nefunguje. Co teď? Na scénu přichází matematika. Konkrétně jednosměrné funkce a s nimi modulární aritmetika. Nemyslím si, že má smysl řešit co je to jednosměrná funkce, ale zkráceně jednosměrná funkce je taková funkce, která se nedá (nebo velmi těžce) zvrátit. Jeko velmi dobrý příklad takové funkce je například smíchání dvou barev (nelze získat zpět původní barvy). Obdobně pro matematické funkce. Zkrátky vždy je o to nalézt takovou funkci, kterou je velmi jednoduché použít a spočítat, ale již velmi složité invertovat výsledek. Právě pro tyto úlohy se perfektně hodí modulární aritmetika. Tam kde se běžná aritmetika chová předvídatelně a na základě pokusů lze konvergovat k výsledku, v modulární takováto chování neexistují.

# Řešení

Následující algoritmus je zhruba použit v šifrách DES (pro velká čísla). Budu však používat malá, aby bylo vše lépe pochopitelné. Alice a Bob se <strong>veřejně</strong> dohodnou na funkci *Y<sup>x</sup>(mod P)*, kdy si číla např. Y=5 a P=8 vymění (a útočník je může odposlechnout).

Teď tedy zná příjemnce, odesílatel i útočník danou funkci. Odesílatel a příjemnce si nyní zvolí jiné číslo <strong>které uchovají v tajnosti</strong>, toto číslo vloží do matematické funkce *Y<sup>x</sup>(mod P)* a výsledek odešlou. Například Bob zvolil x=4, tedy *5<sup>4</sup>(mod 8)=1*. Alice volí x=3, tedy *5<sup>3</sup>(mod 8)=5*. Tyto výsledky si vymění.

Z pohledu útočníka lze říci, že zná funkci a zná také výsledky, konkrétně 1 a 5. S touto znalostí by šlo možná privátní číslo **x** dopočítat. Jenže vyzkoušejte si to. Pro malá čísla možná, ale pro velká je to téměř nemožné. A že se používají velká čísla...

Nyní vezme Alice výsledek od Boba a spočte *vysledek<sup>x</sup>(mod P)*, tedy *1<sup>3</sup>(mod 8)=1*. Nezapomeňte, že číslo **x** je stále privátní a zná ho jen Alice. Stejně teď postupuje i Bob, ale s vlastním privátním číslem a výsledkem od Alice: *5<sup>4</sup>(mod 8)=1*. A zde je vidět k čemu došlo. Výsledek obou výpočtů vyšel stejně a k přenosu priváního čísla **x** nikdy nedošlo. Vyzkoušejte si to na papír a nejlépe pro větší čísla. Pro útočníka nastává velký problém, protože nezná privátní číslo a je pro něj tedy nemožné provést tyto výpočty, nicméně Alice i Bob mají k dispozici jeden výsledek, tedy jeden klíč, který mohou používat.

# Závěr

Ještě zopakuji k čemu tedy došlo. Bylo zapotřebí dohodnout se mezi odesílatelem a příjemcem na společném klíči, podle kterého bude posílaná zpráva šifrována. To se pomocí vhodných matematických metod povedlo a navíc (což je to njdůležitější) není téměř možné tento přenos odposlechnout, jelikož se jedná o jednosměrné operace.

Analogicky lze použít opět míchání barev. Alice a Bob mají nádobu s litrem červené barvy. Stejnou nádobu má i útočník. Alice i Bob nyní nalijí vlastní privátní barvu do nádoby a tyto nádoby si vymění. Útočník je může vidět, ale nedokáže z nich odhadnout jaká je privátní barva. Nakonec Alice i Bob nalijí zbytek své tajné barvy do nádoby (již je mají vyměněné), čímž vznikne Alici i Bobovi stejná barva. Ani Alice, ani Bob, ani útočník neví co bylo přidáno za barvy od toho druhého, ale se znalostí vlastního privátního klíče se dostanou ke stejnému výsledku. útočník tyto klíče nezná a je nahraný.

Tak a zde by měl začínat článek http://zlml.cz/asymetricka-sifra-s-verejnym-klicem.

Tento a daleko více inspirativních nápadů a příběhů naleznete v knize https://www.kosmas.cz/knihy/146743/kniha-kodu-a-sifer/.
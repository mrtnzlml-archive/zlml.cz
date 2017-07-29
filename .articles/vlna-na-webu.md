Vlna je program [Petra Olšáka .{target:_blank}](http://ftp.linux.cz/pub/tex/local/cstug/olsak/vlna/), který slouží k umístění nezalomitelné místo na místo v textu, kde by nemělo dojít k samovolnému zalomení řádku. Tento program slouží k dodatečné úpravě textů napsaných v LaTeXu. V tomto prostředí se nezalomitelná mezera nahrazuje znakem vlnovkou - tildou (~). U webového výstupu se používá zástupná entita <code>&amp;nbsp;</code>.

Kde by měla být nedělitelná mezera
==================================
V základu program Vlna umístí tildu za znaky <code>KkSsVvZzOoUuAI</code>. Více toho pokud vím nedělá. Podle Ústavu pro jazyk český AV ČR by však toto pravidlo mělo platit mimo jiné pro znaky <code>KkSsVvZzAaIiOoUu</code>. Neuvažuji další pravidla, která určují další nevhodné výrazy na konci řádku. Mezi tyto pravidla patří například mezery uvnitř číslic, mezery mezi číslicí a značkou, atd. Některá pravidla jsou totiž natolik specifická, že by je bylo náročné (nebo nepraktické) podchytit programově.

Implementace
============
O samotné nahrazování se stará následující regulární výraz:
```php
preg_replace('<([^a-zA-Z0-9])([ksvzaiou])\s([a-zA-Z0-9]{1,})>i', "$1$2\xc2\xa0$3", $string); //&nbsp; === \xc2\xa0
```
Tento výraz říká, že nestojí-li bezprostředně před sadou znaků <code>KkSsVvZzAaIiOoUu</code> jiný alfanumerický znak a stojí-li za touto sadou jakýkoliv alfanumerický znak oddělený bílým znakem bude tento znak nahrazen entitou <code>&amp;nbsp;</code>. V konkrétní implementaci lze zaregistrovat Vlnu jako helper pro Latte šablony například takto (obsahuje i registraci Texy helperu):

```php
/**
 * @param null $class
 * @return Nette\Templating\ITemplate
 */
protected function createTemplate($class = NULL) {
	$template = parent::createTemplate($class);
	$texy = new \Texy();
	$template->registerHelper('texy', callback($texy, 'process'));
	$template->registerHelper('vlna', function ($string) {
		$string = preg_replace('<([^a-zA-Z0-9])([ksvzaiou])\s([a-zA-Z0-9]{1,})>i', "$1$2\xc2\xa0$3", $string); //&nbsp; === \xc2\xa0
		return $string;
	});
	return $template;
}
```

Vlna se pak v Latte šablonách používá jako jakýkoliv jiný helper:

```
{$post->title|vlna}
```

Ještě by možná stálo za to vrátit se k tomu, jaké problémy by způsobovala implementace i dalších pravidel a jak by to bylo náročné. Ještě nad tím budu přemýšlet, každopádně již teď mě napadají určité problémy. Například u čísel. Jak přesně identifikovat, kdy se má použít nedělitelná mezera a kdy ne? Možná je toto právě ten důvod, proč takové rozšířené chování program Vlna nepodporuje...
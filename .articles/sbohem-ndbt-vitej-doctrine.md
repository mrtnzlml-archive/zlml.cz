Byl jsem požádán, abych napsal nejenom důvod přechodu z Nette Database na Doctrine, ale obecně co mě k tomu vedlo a jak takový přechod vlastně učinit. Na úvod bych však chtěl zdůraznit, že nejsem žádný extra programátor, vlastně to ani nestuduji, takže vše co budu ukazovat a vysvětlovat je tedy z mého pohledu a lecjakého OOP znalce by tento text mohl pobouřit... (-:

Následující text používá [Kdyby\Doctrine](https://github.com/Kdyby/Doctrine), nevidím důvod proč ve spojení s Nette používat něco jiného. Je to dobrá knihovna.

Sbohem NDBT
===========
Nette Database Table a obecně celé Nette Database je úžasná část frameworku a spokojeně jsem ji používal po velmi dlouhou dobu. Nikdy jsem neholdoval pokřikům, že je NDBT zabugované (jako někteří) a i když jsem vyzkoušel i jiné alternativy, vždy jsem se spokojeně vracel právě k NDBT. Použití je velice intuitivní a dobře se s tím zachází:

Model:
```php
class Posts extends Nette\Object {

	/** @var \Nette\Database\Context */
	private $database;

	public function __construct(Nette\Database\Context $context) {
		$this->database = $context;
	}

	public function getAllPosts() {
		return $this->database->table('posts')->where('release_date < NOW()');
	}

}
```

Presenter:
```php
class HomepagePresenter extends BasePresenter {

	/** @var \Posts @inject */
	public $posts;

	public function renderDefault() {
		$this->template->posts = $this->posts->getAllPosts();
	}

}
```

Je to jednoduché a jasné. Takové věci mám prostě rád. V modelové třídě mám jak select metody, tak insert metody, takže bych tomu správně neměl říkat repository, ale abych byl upřímný, tak je mi toto názvosloví celkem volné. Do takové objektu si prostě dám to co chci (tak jak mi to dává smysl). Nepřijde mi to vůbec podstatné. Tahání dat z databáze má však ještě jednu vrstvu a tou je vykreslování.

```html
{foreach $posts as $post}
	<h3><a n:href=":Single:article $post->slug">{$post->title|vlna}</a></h3>
	<p>
		{foreach $post->related('posts_tags')->order('tag_id ASC') as $post_tag}
			<a n:href="Tag:default, $post_tag->tag->name">
				<span style="background: #{$post_tag->tag->color}">{$post_tag->tag->name}</span>
			</a>
		{/foreach}
		{$post->body|truncate:450}
	</p>
{/foreach}
```

A to je věc, která mě dlouhou dobu trápila. Dá se čekat, že když v databázi existuje jakási vazba mezi příspěvkem a tagem, tak že tuto vazbu budu chtít nějak využít. A to pokud možno co nejvíce pohodlně. A co nejvíce pohodlně znamená, že v okamžik, kdy budu pracovat s příspěvkem a vzpomenu si, že potřebuji také tagy, tak tyto tagy také dostanu. Bohužel musím znát také spojovací tabulku, která nemá (minimálně v tomto případě) žádný faktický smysl a celkově práce s takto "dopřivázanou" tabulkou není vůbec pohodlná a už vůbec ne intuitivní. Dává to smysl a asi to tak být musí, takže proti NDBT žádná, ale tak nějak vnitřně jsem hledal něco lepšího (čti více vyhovujícího mým požadavkům).

Vítej Doctrine
==============
Schválně se snažím vše popisovat podle mých myšlenkových pochodů, proto i nadále budu řešit úplně ten samý problém, jen s použitím Doctrine. Nutno ještě dodat, že Doctrine rozhodně nebyla jasná volba. Opět mi dlouho trvalo, než jsem obecně ORM přišel na chuť. Ještě před Doctrine jsem nějakou dobu experimentoval s [Lean Mapperem](http://www.leanmapper.com/) od Vojtěcha Kohouta (Tharos). Malou nevýhodou je, že téměř veškerá dokumentace je v brutálně dlouhém vláknu na Nette fóru, které má v tuto chvíli **1023 příspěvků**, takže je to občas dřina, ale myslím si, že je to skutečně povedená knihovna. Vojtěch Kohout má skutečně dobré myšlenky. Nicméně jsem prostě chtěl přijít Doctrine na chuť, takže jsem i Lean Mapper opustil. Občas dělám radikální změny, pokud by však někdo vyloženě potřeboval důvod k tomu začít s Doctrine (alespoň dočasně), pak tedy jeden mohu nabídnout. A bude velmi krátký. Vidíte někdy jako požadavek na zaměstnance znalost Lean Mapperu, nebo ActiveRow? Pokud ne, tak začněte s tím co se tam ukazuje často. Doctrine.

Ale zpět k tématu. Pojďme si ukázat modelovou část podle mě:
```php
class Posts extends Nette\Object {

	/** @var \Kdyby\Doctrine\EntityDao */
	private $dao;

	public function __construct(Kdyby\Doctrine\EntityDao $dao) {
		$this->dao = $dao;
	}

	public function findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null) {
		return $this->dao->findBy($criteria, $orderBy, $limit, $offset);
	}

}
```

Velmi podobné jako u předchozího modelu. Asi by šlo si napsat nějakou `__call` metodu, protože se dost často volá to co už je v DAO objektu (viz níže), ale kdo si to jak poslepuje, tak to bude mít... A co prezentační vrstva?
```php
class HomepagePresenter extends BasePresenter {

	/** @var \Posts @inject */
	public $posts;

	public function renderDefault() {
		$posts = $this->posts->findBy(array());
	}

}
```

To je také dost podobné. Pole kritérií potom slouží k dodatečnému filtrování ve tvaru např. `['id' => 21]`, tedy předává se to, co se má použít i v SQL WHERE klauzuli. Tento zápis je mi poměrně blízký, protože jsem ho používal při komunikaci s jedním SOAP serverem. Ok, co na to šablona?

```html
{foreach $posts as $post}
	<h3><a n:href=":Single:article $post->slug">{$post->title|vlna}</a></h3>
	<p>
		{foreach $post->tags as $tag}
			<a n:href="Search:default, $tag->name">
				<span style="background: #{$tag->color}">{$tag->name}</span>
			</a>
		{/foreach}
		{$post->body|truncate:450}
	</p>
{/foreach}
```

Tak to je podle mě úplně super výsledek. Maximálně intuitivní a nic víc splňuje to má očekávání. Je však asi zřejmé, že jsem úplně vypustil jakoukoliv informaci o spojovací tabulce. A také jsem ještě neřekl, co je to `EntityDao`, se kterým se pracuje v modelu. Vlastně je to úplně jednoduché. V konfiguračním souboru definuji, že chci pracovat s jakýmsi DAO objektem, tedy objektem, který oproti repository umožňuje data nejen číst, ale také ukládat (opět pouze OOP slovíčkaření).

```neon
services:
	- App\Pictures(@doctrine.dao(Entity\Picture))
	- App\Posts(@doctrine.dao(Entity\Post))
	- App\Tags(@doctrine.dao(Entity\Tag))
	- App\Users(@doctrine.dao(Entity\User))
```

Fajn, teď mám tedy v každé modelové třídě DAO objekt. Ten obsahuje několik metod, které výrazně usnadňují práci s Doctrine ([source](https://github.com/Kdyby/Doctrine/blob/master/src/Kdyby/Doctrine/EntityDao.php)). Do tohoto objektu předávám jakousi entitu. To je objekt, který reprezentuje strukturu databázové tabulky. To možná není napsáno úplně šťastně, ale prakticky to tak skutečně většinou je. Taková entita může vypadat například takto:

```php
namespace Entity;

/**
 * @ORM\Entity
 * @ORM\Table(name="posts")
 */
class Post extends Doctrine\Entities\BaseEntity {

	/**
	 * @ORM\ManyToMany(targetEntity="Tag", inversedBy="posts", cascade={"persist"})
	 * @ORM\JoinTable(name="posts_tags")
	 * @ORM\OrderBy({"name" = "ASC"})
	 */
	protected $tags;

	/**
	 * @ORM\Id
	 * @ORM\Column(type="integer")
	 * @ORM\GeneratedValue
	 */
	protected $id;

	/** @ORM\Column(type="text") */
	protected $title;

	/** @ORM\Column(type="text") */
	protected $slug;

	/** @ORM\Column(type="text") */
	protected $body;

	/** @ORM\Column(type="datetime") */
	protected $date;

}
```

Zde je právě důležité to, že rovnou ukazuji, kde je entita reprezentující tagy (která vypadá podobně jako tato), jak se k ní dostanu a dokonce jak se má řadit. Tedy všechny tyto informace jsem ze šablony odstranil. Pro mě je to tedy úžasný pokrok, protože jsem dosáhl toho co jsem chtěl. Aby se mi s databází pracovalo dobře.

No dobře, ale...
================
A teď je právě čas na některé dotazy, které vznikly při přípravě tohoto článku. Tak například co když chceš položit vlastní dotaz, v Nette Database je to přeci snadné. V Doctrine [také](https://bitbucket.org/mrtnzlml/www.zeminem.cz/src/05dc03f0781fac574de26e128b6509d870b7b789/app/model/Posts.php?at=master&fileviewer=file-view-default#Posts.php-128). V tom by ORM nemělo nijak zásadně bránit...

Další věc je, že v presenteru stále zůstává jistá závislost na struktuře tabulek. Konkrétně opět mluvím o poli kritérií. Jak se úplně zbavit této závislosti a mít pokud možno vše tak, aby když změním strukturu, tak to změním jen někde a ne všude? K tomu se dají použít třeba query objekty, které v sobě drží podobu potřebného SQL dotazu, takže místo toho, abych stále ťukal ten samý dotaz, jen jinde, tak jej schovám do třídy a právě tu pak používám. Budoucí změna se pak pravděpodobně bude týkat právě pouze toho objektu a případně entit. Ono toto asi nejde úplně odstínit (nebo spíš nevím jak), protože vždy je potřeba data i nahrávat a tedy stanovit určitou hranici mezi tím co je závislé na databázi a co už není. Nicméně uvážím-li, že budu měnit strukturu tabulky třeba kvůli tomu, že chci přidat nová data, stejně budu do kódu muset jít a někde ty data vzít a někam je dát. Proto je toto možná úplně zbytečné řešit, protože tato závislost nikdy nepůjde úplně odstranit.

Další věc je trošku záludná. Týká se tříd pro vazební tabulky. Pokud tedy ukládám M:N vazbu jako v předchozím textu, tím myslím, že mi jde pouze o to, že chci uložit do této tabulky cizí klíče, tak se o nic nemusím starat a stačí mi pouze onen dokumentační komentář v entitě `Post` nad proměnnou `protected $tags`, kde je definováno vše potřebné. Problémové je, když chci uložit data i do vazební tabulky. Zde bych rád citoval jeden příspěvek ze StackOverflow, protože si myslím, že tam je vše řečeno naprosto přesně.

> A Many-To-Many association with additional values is not a Many-To-Many, but is indeed a new entity, since it now has an identifier (the two relations to the connected entities) and values.

A přesně takto je s tím tedy potřeba zacházet. Už se nebavíme o vazební tabulce. Už se bavíme o normální tabulce, která vyžaduje svoji entitu a pouze obsahuje dva cizí klíče místo běžného jednoho.

Doufám, že se mi v tomto článku svojí délkou limitně blížící se k nekonečnu podařilo zodpovědět všechny dotazy a objasnit všechny pochybnosti. Pokud ne, můžete se na celou problematiku podívat pod drobnohledem ještě [zde](https://bitbucket.org/mrtnzlml/zlml.cz/src/05dc03f0781fac574de26e128b6509d870b7b789/app/model/?at=master). Máte na celou problematiku jiný názor, nebo to jak to dělám já je kompletně špatně? Sem s tím... (-:
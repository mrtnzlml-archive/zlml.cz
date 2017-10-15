---
timestamp: 1406926545000
title: Za hranicí ORM
slug: za-hranici-orm
---
Již mnohokrát jsem slyšel, že je ORM [antipattern](http://www.zdrojak.cz/clanky/orm-je-antipattern/). Já si to nemyslím. Je to hloupý a uspěchaný názor. V dnešním článku však nechci rozebírat co je a co není antipattern. To je jen zbytečnou ztrátou času. Rád bych ukázal použití jednoho ORM systému, který mě naprosto uchvátil.

# Když jsou potřeba firemní procesy

Zejména začínající firmy mají na svém startu náročný úkol. Je zapotřebí vytvořit vnitrofiremní procesy, které striktně řídí běh různých akcí jako je například výroba, reklamace, nebo třeba koloběh dokumentů. Ve firmě [taková řešení nabízíme](http://www.orgis.cz/sluzby/in-house) a je o ně velký zájem. Má to svůj smysl. Není žádným tajemstvím, že používáme ERP systém [Odoo](https://www.odoo.com/), protože je to velmi silný nástroj. I přes neuvěřitelnou modifikovatelnost této aplikace je občas zapotřebí sáhnout k daleko více sofistikovanému řešení. Proč? Občas jsou totiž vnitropodnikové procesy velmi složité a navíc jich je velké množství. V takovém případě, je téměř jedinou možností napsat si pro tento ERP systém rozšíření, které tento těžký úkol zvládne.

![](https://zlmlcz-media.s3-eu-west-1.amazonaws.com/393212dc-381f-4b3a-a06c-fb5d33d6dc29/workflow.png) *Ukázka struktury klasického firemního workflow*

Nechci však psát návod na to, jak si takový modul naprogramovat. O tom třeba někdy příště. Pojďme se raději podívat na to, jak téměř celé Odoo funguje, protože je to skutečně pozoruhodné. Bez kódů to však nepůjde.

# Záplava tabulek

Abych byl upřímný, tak jsem se ještě nikdy nesetkal s tím, aby byl **každý** objekt v projektu skutečně realizován tabulkou v databázi. Nebo o tom alespoň nevím. Je to v podstatě jako když máte entity a ty jsou pak skutečně v databázi. Rozdíl je však v tom, že zde je v "entitě" i celá potřebná logika (které většinou moc není) a hlavně žádné jiné objekty nejsou potřeba.

V nejprostším tvaru může tedy třída modulu vypadat skutečně pouze jako entita:

```python
class project_wkf_activity(osv.osv):
    _name = 'project.wkf.activity'
    _columns = {
        'sequence': fields.integer('Sequence'),
        'name': fields.char('Workflow Activity Name', required=True, size=64, translate=True),
        'type': fields.many2one('project.wkf.type', 'Workflow Type'),
        'fold': fields.boolean('Folded in Kanban View'),
    }
    # ...
    def jumptoseq(self, cr, uid, ids, sequence, context=None):
    	#...
```

Při programování modulů toto vede k extrémní explozi tabulek v databázi. 500 tabulek uděláte v databázi jako nic. Stačí nainstalovat pár modulů. A to už mi přijde dost netradiční. Kromě modulových tabulek je v základní instalaci asi 100 tabulek, které v sobě drží vše možné, mimo jiné také informace o workflow. A právě definice workflow je to nejzajímavější. Veškerá sranda se totiž odehrává v XML souborech. Ve výsledku stačí pro napsání složitého workflow včetně veškeré logiky pouze XML...

# XML programování (-:

Když jsem toto poprvé viděl, párkrát jsem se zastavil a přemýšlel jsem, jak to vlastně může fungovat. Je to však velmi jednoduché. V inicializačním souboru modulu si stačí nadefinovat jaké XML soubory se mají načítat. Následuje definice workflow. Žádné psaní okolo. Prostě to hned funguje. Začátek takového workflow může vypadat například takto:

```html
<?xml version="1.0" encoding="utf-8"?>
<openerp>
    <data noupdate="0">
    	<record id="wkf1" model="workflow">
            <field name="name">project.wkf1</field>
            <field name="osv">project.project</field>
            <field name="on_create">True</field>
        </record>
	</data>
</openerp>
```

Tím je workflow založeno a při spuštění převedeno do databáze. Jádro má pak za úkol se těchto tabulek chytit a pracovat s nimi. Jsou jasně daná pravidla, takže s tím není žádný problém. Následuje definice akcí (to jsou ty bubliny na obrázku):

```html
<?xml version="1.0" encoding="utf-8"?>
<openerp>
    <data noupdate="0">
    	<!-- viz definice workflow -->
        
        <record id="a_1_0" model="workflow.activity">
            <field name="wkf_id" ref="wkf1"/>
            <field name="flow_start">True</field>
            <field name="name">start-wkf1</field>
            <field name="kind">dummy</field>
        </record>

        <record id="a_1_10" model="workflow.activity">
            <field name="wkf_id" ref="wkf1"/>
            <field name="name">nazev-activity</field>
            <field name="kind">function</field>
            <field name="action">jumptoseq(10)</field>
        </record>
        
        <record id="a_1_500" model="workflow.activity">
            <field name="wkf_id" ref="wkf1"/>
            <field name="flow_stop">True</field>
            <field name="name">end</field>
            <field name="kind">function</field>
            <field name="action">orgis_close()</field>
        </record>
	</data>
</openerp>
```

A následuje definice transitions (spojení na obrázku):

```html
<?xml version="1.0" encoding="utf-8"?>
<openerp>
    <data noupdate="0">
    	<!-- viz definice workflow -->
        <!-- viz definice aktivit -->
        
        <record id="t_1_9" model="workflow.transition">
            <field name="act_from" ref="a_1_40"/>
            <field name="act_to" ref="a_1_50"/>
            <field name="signal">a_1_40toa_1_50</field>
        </record>
        <record id="c_1_7" model="workflow.transition">
            <field name="act_from" ref="a_1_36"/>
            <field name="act_to" ref="a_1_500"/>
            <field name="signal">close_wkf</field>
        </record>
	</data>
</openerp>
```

Ukázky jsou nekompletní, protože by se to sem všechno ani náhodou nevešlo. Výsledný XML soubor vlastně stačí k tomu, aby celé workflow fungovalo. Stačí nadefinovat funkce v příslušných třídách viz například volání `jumptoseq`.

Dost často je také zapotřebí definovat tlačítka pro view, což se dělá také pomocí XML kde je opět originálně vyřešeno přepisování defaultní šablony. Tlačítka mají vždy nějakou akci, která zajistí, že se posuneme ve workflow dále. Na takové workflow lze pověsit úplně všechno. Poslání emailů, vygenerování faktur, založení akce v kalendáři atd. Pokud se jedná o vyloženě automatické kroky, akce se na tlačítku nemusí definovat a pak systém celé workflow proskáče a spustí všechny akce popořadě sám. Zajímavé je však to, že se po spuštění všechno nahrne do databáze a jádro Odoo se strará pouze o to, aby tento interně složitý systém fungoval správně. Všem lidem, kteří nadávají na ORM bych tedy položil následující otázku. Jak uděláte takto sofistikovaný systém bez ORM? Nebo že toto snad není ORM? A uvědomte si, jak jsem se strašně rychle dokázal dostat z ORM až na celý komplexní systém workflow. Je to proto, že jsem vůbec neuvažoval něco jako je ActiveRow... (-: I když z velké části je to spíše plnění tabulek.

V tomto reálně fungujícím řešení je velmi hluboká myšlenka. A udivuje mě, že v jiných systémech toto funguje úplně obráceně. Všichni se snaží programovat spíše to jádro. Ukazuje se však, že využít kvalitní jádro, které se stará o všechno je daleko přínosnější. Vzpomeňte si na tento článek, až budete ťukat do klávesnice entity, mappery, repository, DAO objekty a další a to pouze kvůli tomu, aby bylo možné udělat nějaké takové workflow, jako jsem zde teď popsal. Už zase...

Používáte také nějakou netradiční implementaci ORM?
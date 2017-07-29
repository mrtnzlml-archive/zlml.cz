Dnešní článek bude spíše zápisek, protože jsem řešení tohoto problému hledal neskutečně dlouho a jak se později ukázalo, tak řešení je sice jednoduché, ale je zakořeněné hluboko v jádru Odoo ERP systému. O co jde. Občas je potřeba schovat záhlaví (viz obrázek) u některých modulů.

[* ddfc321c-2190-4cf6-98a9-89852713e626/vystrizek.png 500x? <]

Tento panel má sice dobrý důvod, ale existují případy, kde je prostě nadbytečný. Typický případ takové nadbytečnosti je modul Dashboards (technický název `board`) kdy je tento prostor nijak nevyužívaný. Zřejmě by tento problém šel řešit nějaký hackem, ale to prostě není dobře. Problém je [zde](https://github.com/odoo/odoo/blob/8.0/addons/web/static/src/js/views.js#L905). "Special case for Dashboards"...

Jak na to
=========
Asi úplně nejjasnější bude, když popíšu posloupnost kroků, které vedou ke správnému řešení. Nejedná se o nic kompikovaného. Všechny níže uváděné postupy jsou klasické postupy při vývoji modulu. Jen je (do teď) pravděpodobně nikde nenajdete, nebo nad tím zbytečně vytuhnete na zoufale dlouhou dobu. Ostatně [podívejte se](https://searchcode.com/?q=views_switcher%20lang:Javascript), jak je výskyt tohoto kousku užitečného kódu [používaný](https://github.com/odoo/odoo/search?l=javascript&q=views_switcher&type=Code&utf8=%E2%9C%93) v public repozitářích... :-)

<span style="font-size:2em">1.</span> Registrace XML definice v `__openerp__.conf`

Tato záležitost je asi celkem jasná. Jednoduše musíme definovat, že se má při compile-time brát ohled na XML soubor, ve kterém zaregistrujeme JS soubor viz další bod.

```python
{
    #...
    
    'data': [
        'views/header.xml',
    ],
    
    #...
}
```

<span style="font-size:2em">2.</span> Registrace JS souboru

To jsem to ale nazval blbě... (-: V předchozím bodě je tedy definovanám soubor v podadresáři `views`, jehož obsah je např. takovýto:

```html
<?xml version="1.0" encoding="utf-8"?>
<openerp>
    <data>
        <template id="assets_backend" name="queue assets" inherit_id="web.assets_backend">
            <xpath expr="." position="inside">
                <script type="text/javascript" src="/module_name/static/src/js/header.js"/>
            </xpath>
        </template>
    </data>
</openerp>
```

Tato registrace je naprosto běžná. ERP se k tomu pak staví poměrně chytře, takže když je ERP v `?debug=` módu, tak souboru vrací tak jak jsou, jinak je všechny skládá do jednoho a provádí minimalizaci. V tomto případě je rozdíl signifikantní.

<span style="font-size:2em">3.</span> Javascript definice

Je známá věc, že si toto ERP bez JS ani neuprdne. Na jednu stranu mě to trošku štve, na druhou stranu to nemá vůbec smysl řešit. Dalším krokem proto bude definice na straně JS, která zakáže tomuto konkrétnímu view vykreslení headeru:

```javascript
openerp.module_name = function (instance) {
    //var QWeb = instance.web.qweb;
    if (!instance.module_name) {
        instance.module_name = {};
    }

    //zde navíc např. definice pro instance.web.qweb

    instance.web.ViewManagerAction.include({
        init: function(parent, action) {
            var flags = action.flags || {};
            if (action.res_model == 'module_model' && action.view_mode === 'form') {
                _.extend(flags, {
                    views_switcher : false,
                    display_title : false,
                    search_view : false,
                    pager : false,
                    sidebar : false,
                    action_buttons : false
                });
            }
            action.flags = flags
            this._super(parent, action);
        },
    });
}
```

Toto nastavení je vlastně úplně to stejné, jako je v jádru. Jedná se o naprosto korektní a čisté řešení. Bohužel je nutné jej řešit touto myškou, protože toto není funkce, která je (nebo by do budoucna měla být) přímo podporována. Dává to smysl, protože se jedná o skutečně krajní případ.

A na závěr mám pro všechny čtenáře třešničku v podobě easter eggu. Vyzkoušejte si doplnit do URL parametr `?kitten=`, stejně jako se doplňuje například ten parametr pro zapnutí debug režimu... (-: *#yourewelcome*
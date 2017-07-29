Tento článek ve skutečnosti odstartovalo zdánlivě nesouvisející vlákno na [Nette fóru .{target:_blank}](http://forum.nette.org/cs/19397-ako-sa-zbavit-fid-v-url-ak-sa-nemylim). V tomto vláknu se řeší parametr *_fid* v URL adrese, který tam Nette framework přikládá kvůli flash messages. Tato vlastnost někoho skutečně hodně štve, mě zase až tak moc ne. Jenže když jsem nad tím vláknem chvíli seděl, tak jsem si uvědomil, že mám komentářový systém Disqus implementovaný špatně. Čtěte dál a vyhněte se stejné chybě... (-:

Univerzální kód
===============
Disqus poskytuje "by default" univerzální kód, který prakticky pouze zkopírujete na svůj web na požadované místo a je hotovo. Tento kód vypadá zhruba takto:

```html
<div id="disqus_thread"></div>
<script type="text/javascript">
    /* * * CONFIGURATION VARIABLES: EDIT BEFORE PASTING INTO YOUR WEBPAGE * * */
    var disqus_shortname = ''; // required: replace it with your forum shortname

    /* * * DON'T EDIT BELOW THIS LINE * * */
    (function() {
        var dsq = document.createElement('script'); dsq.type = 'text/javascript'; dsq.async = true;
        dsq.src = '//' + disqus_shortname + '.disqus.com/embed.js';
        (document.getElementsByTagName('head')[0] || document.getElementsByTagName('body')[0]).appendChild(dsq);
    })();
</script>
<noscript>Please enable JavaScript to view the <a href="http://disqus.com/?ref_noscript">comments powered by Disqus.</a></noscript>
<a href="http://disqus.com" class="dsq-brlink">comments powered by <span class="logo-disqus">Disqus</span></a>
```

Je to pohodlné, ale je to úplně blbě. Nejenom pro Nette aplikace, ale myslím si, že tak nějak celkově pro všechny aplikace. Fungovat to sice bude, to ano. Ale jen tak zdánlivě. Problém je totiž v tom, že toto nastavení bere jako identifikátor diskuse URL adresu a pokud se jen drobně změní, tak se založí nová diskuse. Přehled těchto diskusí je pak vidět v Disqus administraci. To s sebou nese celou řadu problémů. Diskuse nejde pořádně zamknout a už to, že k jedné stránce může být více diskusí je problém. Každá diskuse totiž musí mít unikátní identifikátor [disqus_identifier .{target:_blank}](https://help.disqus.com/customer/portal/articles/472098-javascript-configuration-variables) nezávisle na parametrech (pokud ovšem tyto parametry nejsou žádoucí).

Lazy loading
============
Já jsem sice nepoužil defaultní konfiguraci, ale udělal jsem prakticky tu samou chybu. Teď ale konečně k lazy loadingu. Na svém blogu to již používám dlouhou dobu a myslím si, že se to již osvědčilo. Inspirací k mé implementaci je [tento gist .{target:_blank}](https://gist.github.com/omgmog/2310982).

Stačí umístit následující kód do nějakého souboru *main.js*, který se spouští po načtení stránky:

```javascript
var disqus_div = $("#disqus_thread");
if (disqus_div.size() > 0) {
    var ds_loaded = false,
    top = $('.load_disqus').offset().top, //upravit podle potřeby
    disqus_data = disqus_div.data(),
    check = function () {
        if (!ds_loaded && $(window).scrollTop() + $(window).height() > top) {
            ds_loaded = true;
            for (var key in disqus_data) {
                if (key.substr(0, 6) == 'disqus') {
                    window['disqus_' + key.replace('disqus', '').toLowerCase()] = disqus_data[key];
                }
            }
            var dsq = document.createElement('script');
            dsq.type = 'text/javascript';
            dsq.async = true;
            dsq.src = 'http://' + window.disqus_shortname + '.disqus.com/embed.js';
            (document.getElementsByTagName('head')[0] || document.getElementsByTagName('body')[0]).appendChild(dsq);
        }
    };
    $(window).scroll(check);
    check();
}
```

Tím to však nekončí. Je samozřejmě nutné určit kde se Disqus bude zobrazovat:

```html
<div class="hidden-print">
	<div id="disqus_thread" data-disqus-shortname="mrtnzlml" data-disqus-url="{link //this}"></div>
	<noscript>Please enable JavaScript to view the <a href="http://disqus.com/?ref_noscript">comments powered by Disqus.</a></noscript>
	<a href="http://disqus.com" class="dsq-brlink">comments powered by <span class="logo-disqus">Disqus</span></a>
</div>
```

Klíčový je zde právě data atribut *disqus-url*. No a aby byl kod kompletní, tak je zapotřebí někam umístit CSS trídu *.load_disqus*. Tu doporučuji umístit někam nad diskusi a tím myslím třeba o celou viditelnou stránku. Disqus se tak začne načítat o něco dříve, než k němu čtenář doscrolluje, takže se stihne načíst a nebude to rušit. Ve výsledku se tedy Disqus nenačítá po otevření stránky, takže je načtení svižné, ale po např. přečtení článku je již načtený...

A co vy? Máte Disqus na svém webu implementovaný správně? (-:
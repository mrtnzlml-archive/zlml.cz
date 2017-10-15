---
timestamp: 1377116084000
title: AJAX upload souborů v Nette pomocí Fine Uploaderu
slug: ajax-upload-souboru-v-nette-pomoci-fine-uploaderu
---
<div class="alert alert-danger">Následující text řeší starší verzi FineUploaderu `3.*`, nikoliv nejnovější. Hledáte-li aktuálnější návod, přečtěte si prosím http://zlml.cz/ajax-upload-souboru-v-nette-pomoci-fine-uploaderu-2...</div>

A je zde další ukázka vlastní práce, která se může hodit i někomu dalšímu. Tentokrát půjde o ajaxové
nahrávání souborů v Nette pomocí [Fine Uploaderu .{target:_blank}](http://fineuploader.com/).
Obecně to není moc jednoduchá sranda, ale uvidíte, že to zase není taková věda...

A jak už to tak dělávám, lepší než spoustu povídání je spousta ukázek. Prvně je potřeba nalinkovat
soubory Fine Uploaderu, nette.ajaxu a vlastního javascriptového souboru:

```html
<script src="{$basePath}/js/jquery.fineuploader-3.7.0.min.js"></script>
<script src="{$basePath}/js/nette.ajax.js"></script>
<script src="{$basePath}/js/main.js"></script>
```

Použití samotného Fine Uploaderu je nesmírně jednoduché. Nejdříve je třeba vytvořit element na který
se uploader později zavěsí a případně tlačítko na upload, pokud nechceme soubory uploadovat rovnou:

```html
<div id="image-uploader"{ifset $selected} data-id="{$selected}"{/ifset}></div>
<div id="triggerUpload">Nahrát obrázky</div>
```

Přichází na řadu samotné oživení uploaderu pomocí javascriptu (soubor main.js):

```javascript
$(function () {
	if ($('#image-uploader').length != 0) { //test existence elementu
		$.nette.ext('uploader', {
			complete: function () { //zavěšení na konec ajaxového požadavku
				var uploader = $('#image-uploader').fineUploader({
					request: {
						endpoint: 'product/default/' + $('#image-uploader').data('id') + '?do=upload'
					},
					text: {
						uploadButton: 'Klikněte, nebo přetáhněte obrázky',
						cancelButton: 'zrušit',
						failUpload: 'Nahrání obrázku se nezdařilo',
						dragZone: 'Přetáhněte soubory sem',
						dropProcessing: 'Zpracovávám přetažené soubory...',
						formatProgress: '{percent}% z {total_size}',
						waitingForResponse: 'Zpracovávám...'
					},
					autoUpload: false,
					failedUploadTextDisplay: {
						mode: 'custom',
						maxChars: 70,
						responseProperty: 'error',
						enableTooltip: true
					}
				});
				$('#triggerUpload').click(function () {
					uploader.fineUploader('uploadStoredFiles');
				});
			}
		});
	}

	$.nette.init(); //inicializace nette.ajax
});
```

Protože jsem v mém případě donačítal tento upload element ajaxově, musel jsem script pro uploader
zavěsit na nette.ajax událost complete. Tato obálka se dá smazat a spouštět klasicky
při události document.ready. Je zde spoustu, pro samotnou funkčnost, zbytečného kódu.
Podstatný je pouze request:endpoint, který ukazuje na URL adresu aplikace, kde čeká Nette handle.
Ten může vypadat například takto:

```php
public function handleUpload($id) {
	$allowedExtensions = array("jpeg", "jpg", "png", "gif"); //například pro obrázky
	$uploader = new \qqFileUploader($allowedExtensions);
	//...
	try {
		$result = $uploader->handleUpload(__DIR__ . '/../../../www/uploads/' . $id . '/default', NULL);
		$result['uploadName'] = $uploader->getUploadName();
		//...
	} catch (\Exception $exc) {
		$this->sendResponse(new \Nette\Application\Responses\JsonResponse(array(
			'error' => $exc->getMessage(),
		)));
	}
	$this->invalidateControl();
	$this->sendResponse(new \Nette\Application\Responses\JsonResponse($result));
}
```

Opět jsem vypustil části, které nejsou úplně důležité pro samotnou funkčnost. Jde pouze o to mít
připravenou handle metodu, která převezme například ID, důležité však je, že spouští metodu handleUpload()
a odesílá JSON odpověď a to jak errorovou, tak normální, což je následně na straně klienta vyhodnoceno
jako úspěšný upload.

V kódu je zmíněna také třída qqFileUploader. Tu naleznete například na [GitHubu .{target:_blank}](https://github.com/Widen/fine-uploader-server) a nejenom pro PHP. Já jsem si tuto třídu obohatil pouze
o webalize názvů souborů.

A to je vlastně úplně celé. Stačí tedy spustit Fine Uploader na straně klienta například
podle oficiálních návodů, endpoint nastavit na nějaký handle v aplikaci a ten správně použít.
To konkrétně obnáší odeslání JSON odpovědi o úspěšném zpracování obrázku.
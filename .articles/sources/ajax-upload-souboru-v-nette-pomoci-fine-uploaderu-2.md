---
timestamp: 1393877713000
title: AJAX upload souborů v Nette pomocí Fine Uploaderu #2
slug: ajax-upload-souboru-v-nette-pomoci-fine-uploaderu-2
---
[Dříve](ajax-upload-souboru-v-nette-pomoci-fine-uploaderu) jsem psal o tom, jak použít Fine Uploader jakožto nástroj pro AJAXové nahrávání souborů na server. Původní článek však platí pouze pro verzi `3.*`, která je dnes již zastaralá. Pojďme si dnes ukázat v podstatě to samé, ale pro novější verzi `4.3+`, která se v učitých směrech poměrně zásadně liší od svého předchůdce. Tentokrát se to však pokusím vyřešit co nejjednodušeji.

Začátek je vlastně úplně stejný. Musíme nalinkovat javascriptové soubory:

```html
<!-- jQuery -->
<script src="{$basePath}/js/jquery.fineuploader-4.3.1.min.js"></script>
<script src="{$basePath}/js/nette.ajax.js"></script>
<script src="{$basePath}/js/main.js"></script>
```

Použití je úplně jednoduché, ve zjednodušené formě:

```html
<div id="image-uploader"></div>
```

Snažím se ukázat opravdu jen kritické minimum, protože ty základní věci jsou stejné, případně dohledatelné v dokumentaci, takže se dají oba dva návody z velké části doplnit. Minule jsem však zatáhl do ukázek i poměrně hodně balastu, takže ten u staré verze nechám, ale bude následovat opravdu jen to nejnutnější.

Stejně tedy jako v předchozí verzi následuje javascriptový spouštěcí kód. Zde již vznikají určité odlišnosti:

```javascript
$(function () {
	$('#image-uploader').fineUploader({
		debug: true, //hodí se pro lazení
		request: {
			endpoint: 'pictures?do=uploadPicture'
		},
		retry: {
			enableAuto: true
		}
	});
});
```

Použití je tedy téměř stejné, až na to, že jsem úplně vypustil překlad textů. V této nové verzi jsou totiž novinkou šablony (ostatně proto také nové číslo verze). Uživatel-programátor má tak více pod kontrolou výsledný vzhled uploaderu:

```html
<script type="text/template" id="qq-template">
	<div class="qq-uploader-selector qq-uploader">
		<div class="qq-upload-drop-area-selector qq-upload-drop-area" qq-hide-dropzone>
			<span>Přetáhněte soubory sem</span>
		</div>
		<div class="qq-upload-button-selector qq-upload-button">
			<div>Klikněte, nebo přetáhněte obrázky</div>
		</div>
        <span class="qq-drop-processing-selector qq-drop-processing">
           <span>Zpracovávám přetažené soubory...</span>
           <span class="qq-drop-processing-spinner-selector qq-drop-processing-spinner"></span>
        </span>
		<ul class="qq-upload-list-selector qq-upload-list">
			<li>
				<div class="qq-progress-bar-container-selector">
					<div class="qq-progress-bar-selector qq-progress-bar"></div>
				</div>
				<span class="qq-upload-spinner-selector qq-upload-spinner"></span>
				<img class="qq-thumbnail-selector" qq-max-size="100" qq-server-scale>
				<span class="qq-edit-filename-icon-selector qq-edit-filename-icon"></span>
				<span class="qq-upload-file-selector qq-upload-file"></span>
				<input class="qq-edit-filename-selector qq-edit-filename" tabindex="0" type="text">
				<span class="qq-upload-size-selector qq-upload-size"></span>
				<a class="qq-upload-cancel-selector qq-upload-cancel" href="#">Zrušit</a>
				<a class="qq-upload-retry-selector qq-upload-retry" href="#">Opakovat</a>
				<a class="qq-upload-delete-selector qq-upload-delete" href="#">Smazat</a>
				<span class="qq-upload-status-text-selector qq-upload-status-text"></span>
			</li>
		</ul>
	</div>
</script>
```

A opět následuje zpracování v handleru:

```php
public function handleUploadPicture() {
	$uploader = new \UploadHandler();
	$uploader->allowedExtensions = array("jpeg", "jpg", "png", "gif");
	$result = $uploader->handleUpload(__DIR__ . '/../../www/uploads');
	$this->sendResponse(new Nette\Application\Responses\JsonResponse($result));
}
```

Zde celkem není co pokazit, ale pokud by bylo potřeba vrátit chybu, provede se to opět pomocí `JsonResponse`:

```php
$this->sendResponse(new Nette\Application\Responses\JsonResponse(array(
		'error' => $exc->getMessage(),
)));
```

Samotná třída `UploadHandler` je pak opět k nalezení na [GitHubu](https://github.com/Widen/fine-uploader-server/blob/master/php/traditional/handler.php). Tento návod tedy mohu zakončit vlastní citací:

> A to je vlastně úplně celé. Stačí tedy spustit Fine Uploader na straně klienta například podle oficiálních návodů, endpoint nastavit na nějaký handle v aplikaci a ten správně použit. To konkrétně obnáší odeslání JSON odpovědi o úspěšném zpracování obrázku.
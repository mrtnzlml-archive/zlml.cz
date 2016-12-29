import "codemirror/lib/codemirror.css";
import "codemirror/theme/monokai.css";
import CodeMirror from "codemirror";
import "nette.ajax.js";
import qq from "fine-uploader";

$(document).ready(function () {
	$.nette.init();

	let uploaderDiv = document.getElementById('image-uploader');
	if (uploaderDiv) {
		let uploader = new qq.FineUploader({
			//debug: true,
			element: uploaderDiv,
			request: {
				endpoint: 'pictures?do=uploadReciever'
			},
			retry: {enableAuto: true},
			chunking: {enabled: true},
			resume: {enabled: true}
		});
	}

	let editorDiv = document.getElementById("editor");
	if (editorDiv) {
		let editor = CodeMirror.fromTextArea(editorDiv, {
			lineWrapping: true,
			mode: "text/html"
		});

		$('input[name=pic]').click(function (e) {
			e.preventDefault();
			editor.replaceSelection('[* ' + $(this).data('source') + ' 200x? <]');
			editor.focus();
		});

		if (!editor.getValue() && window.localStorage) {
			var loc = location.pathname + location.search;
			if (localStorage.getItem('content:' + loc) != undefined) {
				editor.setValue(localStorage.getItem('content:' + loc));
			}
			$('[name="title"]').val(localStorage.getItem('title:' + loc));
			$('[name="slug"]').val(localStorage.getItem('slug:' + loc));
			$('[name="tags"]').val(localStorage.getItem('tags:' + loc));
		}


		let timer = null;
		let form = document.getElementById('frm-postForm-postForm'); //FIXME: not very good idea
		let updateUrl = form.dataset.previewUpdateUrl;

		updatePreview(updateUrl, editor);

		$('textarea, [name="title"], [name="tags"]').on('change keyup paste', function (e) {
			if (timer) {
				window.clearTimeout(timer);
			}
			timer = window.setTimeout(function () {
				timer = null;
				updatePreview(updateUrl, editor);
				if (window.localStorage) {
					var loc = location.pathname + location.search;
					localStorage.setItem('title:' + loc, $('[name="title"]').val());
					localStorage.setItem('slug:' + loc, $('[name="slug"]').val());
					localStorage.setItem('content:' + loc, editor.getValue());
					localStorage.setItem('tags:' + loc, $('[name="tags"]').val());
				}
				e.preventDefault();
			}, 1000);
		});
	}

	// generuje URL na zaklade zadavaneho titulku
	$('input[data-slug-to]').keyup(function () {
		var slugId = $(this).data('slug-to');
		var val = $(this).val();
		$('#' + slugId).val(make_url(val));
	});

});

var nodiac = {
	'á': 'a',
	'č': 'c',
	'ď': 'd',
	'é': 'e',
	'ě': 'e',
	'í': 'i',
	'ň': 'n',
	'ó': 'o',
	'ř': 'r',
	'š': 's',
	'ť': 't',
	'ú': 'u',
	'ů': 'u',
	'ý': 'y',
	'ž': 'z'
};
/** Vytvoření přátelského URL
 * @param string řetězec, ze kterého se má vytvořit URL
 * @return string řetězec obsahující pouze čísla, znaky bez diakritiky, podtržítko a pomlčku
 * @copyright Jakub Vrána, http://php.vrana.cz/
 */
function make_url(s) {
	s = s.toLowerCase();
	var s2 = '';
	for (var i = 0; i < s.length; i++) {
		s2 += (typeof nodiac[s.charAt(i)] != 'undefined' ? nodiac[s.charAt(i)] : s.charAt(i));
	}
	return s2.replace(/[^a-z0-9_]+/g, '-').replace(/^-|-$/g, '');
}

function insertTag(tagname) {
	var input = $('[name="tags"]');
	if ($.trim(input.val()).length > 0) {
		input.val(input.val() + ', ' + tagname);
	} else {
		input.val(tagname);
	}
}

function updatePreview(updateUrl, editor) {
	$.nette.ajax({
		url: updateUrl,
		type: 'post',
		data: {
			title: $('[name="title"]').val(),
			content: editor.getValue(),
			tags: $('[name="tags"]').val(),
		}
	});
}

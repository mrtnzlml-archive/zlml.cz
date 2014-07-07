$(function () {

	$("h3[id|=toc]").each(function () {
		$(this).append($('<a class="anchor hidden-print"> #</a>').attr("href", "#" + $(this).attr("id")))
	});

	$('body').on('click', '[data-confirm]', function (e) {
		var question = $(this).data('confirm');
		if (!confirm(question)) {
			e.stopImmediatePropagation();
			e.preventDefault();
		}
	});

	$('#qr').qrcode({
		render: 'image',
		text: document.URL,
		radius: 0.5,
		size: 107
	});

	var disqus_div = $("#disqus_thread");
	if (disqus_div.size() > 0) {
		var ds_loaded = false;
		var top = $('.load_disqus').offset().top;
		var disqus_data = disqus_div.data();
		var check = function () {
			//if there is #comment hash in url load disqus directly, otherwise load it lazy way
			if (!ds_loaded && (window.location.hash.indexOf('comment') > -1 || $(window).scrollTop() + $(window).height() > top)) {
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

	// generuje URL na zaklade zadavaneho titulku
	$('input[data-slug-to]').keyup(function () {
		var slugId = $(this).data('slug-to');
		var val = $(this).val();
		$('#' + slugId).val(make_url(val));
	});

	var a, b = $("#pocet").eq(0),
		d = parseInt($(b).text(), 10);
	var time = $("#time"),
		minuty = $("#minuty"),
		tip = $("#tip");
	$(document).on('scroll', function () {
		a && clearTimeout(a), a = setTimeout(function () {
			var a = $("#columns").height(),
				e = d / a,
				f = $(this).scrollTop(),
				g = a - f,
				h = Math.ceil(g * e),
				min = Math.ceil(h / 180);
			h >= 0 ? (b.text(h), time.text(min)) : (b.text("0"), time.text("0"));
			switch (min) {
				case 1:
					tip.text("Dočtěte tento text.");
					minuty.text("minuta");
					break;
				case 2:
					tip.text("Napijte se.");
					minuty.text("minuty");
					break;
				case 3:
					minuty.text("minuty");
					tip.text("Uvařte si kafe.");
					break;
				case 4:
					tip.text("Nasvačte se.");
					minuty.text("minuty");
					break;
				default:
					tip.text("Přečtěte si komentáře.");
					minuty.text("minut");
			}
		}, 100)
	});

});

var nodiac = { 'á': 'a', 'č': 'c', 'ď': 'd', 'é': 'e', 'ě': 'e', 'í': 'i', 'ň': 'n', 'ó': 'o', 'ř': 'r', 'š': 's', 'ť': 't', 'ú': 'u', 'ů': 'u', 'ý': 'y', 'ž': 'z' };
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
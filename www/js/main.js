$(function(){

	$.nette.init();

    $('body').on('click', '[data-confirm]', function (e) {
        var question = $(this).data('confirm');
        if (!confirm(question)) {
            e.stopImmediatePropagation();
            e.preventDefault();
        }
    });

	$('#qr').qrcode({
		text: document.URL,
		radius: 0.5,
		size: 107
	});

	$("#outline, #outline2").fracs("outline", {
		crop: true,
		styles: [
			{selector:"p",fillStyle:"rgb(230,230,230)"},
			{selector:"pre",fillStyle:"rgb(200,200,200)"},
			{selector:"a,h1,h2,h3,h4,h5,h6",fillStyle:"rgb(104,169,255)"},
			{selector:"canvas",fillStyle:"rgb(108,196,46)"},
			{selector:"blockquote,.thumbnail,#disqus_thread",fillStyle:"rgb(221,75,57)"},
			{selector:"table",fillStyle:"rgb(200,200,30)"}

		],
		viewportStyle:{fillStyle:"rgba(104,169,255,0.2)"},
		viewportDragStyle:{fillStyle:"rgba(104,169,255,0.5)"}
	});

	var fixAffix = function() {
		return $('#bottom').outerHeight(true) + $('.footer').outerHeight(true) + 40;
	}
	$('#outline').affix({
		offset: {
			top: 351,
			bottom: function () {
				return (this.bottom = fixAffix);
			}
		}
	})
	$(window).scroll(fixAffix);

	$('#outline2').affix({
		offset: {
			top: 264,
			bottom: 100
		}
	})

	var disqus_div = $("#disqus_thread");
	if (disqus_div.size() > 0 ) {
		var ds_loaded = false,
			top = $('.load_disqus').offset().top,
			disqus_data = disqus_div.data(),
			check = function(){
				if ( !ds_loaded && $(window).scrollTop() + $(window).height() > top ) {
					ds_loaded = true;
					for (var key in disqus_data) {
						if (key.substr(0,6) == 'disqus') {
							window['disqus_' + key.replace('disqus','').toLowerCase()] = disqus_data[key];
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

});
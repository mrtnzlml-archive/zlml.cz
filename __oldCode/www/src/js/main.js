$(function () {

	var bodyEl = $('body');

	$("h3[id|=toc]").each(function () {
		$(this).append($('<a class="anchor hidden-print"> #</a>').attr("href", "#" + $(this).attr("id")))
	});

	bodyEl.on('click', '[data-confirm]', function (e) {
		let question = $(this).data('confirm');
		if (!confirm(question)) {
			e.stopImmediatePropagation();
			e.preventDefault();
		}
	});

	bodyEl.on('click', 'a[target^="_new"]', function (e) {
		var top = window.screenY + (window.outerHeight - 500) / 2;
		var left = window.screenX + (window.outerWidth - 650) / 2;
		window.open(this.href, 'newwindow', 'width=650, height=500, top=' + top + ', left=' + left);
		e.stopImmediatePropagation();
		e.preventDefault();
	});

	$('.toggleHelp').click(function () {
		$('.help').toggle('fast');
	});

	var disqus_div = $("#disqus_thread");
	if (disqus_div.length > 0) {
		var disqus_data = disqus_div.data();
		for (var key in disqus_data) {
			if (key.substr(0, 6) == 'disqus') {
				window['disqus_' + key.replace('disqus', '').toLowerCase()] = disqus_data[key];
			}
		}
		var dsq = document.createElement('script');
		dsq.type = 'text/javascript';
		dsq.async = true;
		dsq.src = '//' + window.disqus_shortname + '.disqus.com/embed.js';
		(document.getElementsByTagName('head')[0] || document.getElementsByTagName('body')[0]).appendChild(dsq);
	}

});

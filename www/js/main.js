$(function(){

    $('body').on('click', '[data-confirm]', function (e) {
        var question = $(this).data('confirm');
        if (!confirm(question)) {
            e.stopImmediatePropagation();
            e.preventDefault();
        }
    });

	$('#qr').qrcode({
		text: 'http://www.zeminem.cz/',
		radius: 0.5,
		size: 107
	});

	$("#outline").fracs("outline", {
		crop: true,
		styles: [
			{selector:"p",fillStyle:"rgb(230,230,230)"},
			{selector:"pre",fillStyle:"rgb(200,200,200)"},
			{selector:"a,h1,h2,h3,h4,h5,h6",fillStyle:"rgb(104,169,255)"},
			{selector:"canvas",fillStyle:"rgb(108,196,46)"},
			{selector:"blockquote, .thumbnail, #disqus_thread",fillStyle:"rgb(221,75,57)"}

		],
		viewportStyle:{fillStyle:"rgba(104,169,255,0.2)"},
		viewportDragStyle:{fillStyle:"rgba(104,169,255,0.5)"}
	});

	$('#outline').affix({
		offset: {
			top: 350,
			bottom: function () {
				return (this.bottom = $('#disqus_thread').outerHeight(true) + 800)
			}
		}
	})

});
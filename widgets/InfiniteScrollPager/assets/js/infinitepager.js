window.mata.infinitePager = window.mata.infinitePager || {};
mata.infinitePager.content;
mata.infinitePager.nextPageUrl;
mata.infinitePager.isLoading = false;

mata.infinitePager.init = function(opts) {

	mata.infinitePager.nextPageUrl = $('#'+opts.listViewId).children('ul.pagination').find('li.next a').attr('href');

	$('#'+opts.pjax.id).on('pjax:end', function(e, contents, options) {
		$('#'+opts.listViewId).prepend(mata.infinitePager.content);
		window.top.mata.simpleTheme.ajaxLoader.stop();	
		mata.infinitePager.isLoading = false;
		mata.simpleTheme.onPjaxSuccess();
	});
	
	$('#'+opts.pjax.id).on('pjax:beforeReplace', function(e, contents, options) {
		mata.infinitePager.content = $('#'+opts.pjax.id).contents().children('div[data-key]');
		mata.infinitePager.nextPageUrl = $('#'+opts.pjax.id).contents().children('ul.pagination').find('li.next a').attr('href');
		e.stopPropagation();
		return false;
	});

	$('body').on("scroll", function() {
    	if($(opts.itemSelector, '#'+opts.listViewId).length > 2) {
    		var hT = $(opts.itemSelector, '#'+opts.listViewId).eq(-2).offset().top,
			hH = $(opts.itemSelector, '#'+opts.listViewId).eq(-2).outerHeight(),
			wH = $('body').height(),
			wS = $(this).scrollTop()+(hH);

			if (wS > (hT+hH-wH) && mata.infinitePager.nextPageUrl != undefined && !mata.infinitePager.isLoading){
				$('#'+opts.listViewId).children('ul.pagination').find('li.next a').trigger('click');
				mata.infinitePager.isLoading = true;
				window.top.mata.simpleTheme.ajaxLoader.run();	
			}
    	}
		
	});
	
}

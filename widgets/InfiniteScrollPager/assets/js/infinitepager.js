window.mata.infinitePager = window.mata.infinitePager || {};
mata.infinitePager.content;
mata.infinitePager.nextPageUrl;
mata.infinitePager.isLoading = false;

mata.infinitePager.init = function(opts) {

	mata.infinitePager.nextPageUrl = $('#'+opts.listViewId).children('ul.pagination').find('li.next a').attr('href');

	$(window).on("scroll", function() {
    	if($(opts.itemSelector, '#'+opts.listViewId).length > 2) {
    		var hT = $(opts.itemSelector, '#'+opts.listViewId).eq(-2).offset().top,
			hH = $(opts.itemSelector, '#'+opts.listViewId).eq(-2).outerHeight(),
			wH = $(window).height(),
			wS = $(window).scrollTop()+(hH);

			if (wS > (hT+hH-wH) && mata.infinitePager.nextPageUrl != undefined && !mata.infinitePager.isLoading) {
				getNextPage(opts);
			}
    	}
	});

	function getNextPage(opts) {

		var nextLink = $('#'+opts.listViewId).children('ul.pagination').find('li.next a');
		nextLink.trigger('click');

		mata.infinitePager.isLoading = true;
		$('#'+opts.listViewId).find(".loader").show();
		$('#'+opts.pjax.id).one('pjax:end', function(e, contents, options) {

			var newContent = $('#'+opts.listViewId).find("> div:not(.loader)")
			var newContentHeight = $('#'+opts.listViewId).outerHeight(true);
			newContent.css("opacity", 0)
			$('#'+opts.listViewId).prepend(mata.infinitePager.content);
			mata.infinitePager.isLoading = false;
			$('#'+opts.listViewId).find(".loader").hide();
			$('body').scrollTop(mata.infinitePager.bodyHeight);

			mata.simpleTheme.onPjaxSuccess();

			newContent.each(function(i, el) {
				$(el).hide().css("opacity", 1).delay(i * 100).fadeIn(300);
			})
		});
		
		$('#'+opts.pjax.id).one('pjax:beforeReplace', function(e, contents, options) {
			mata.infinitePager.content = $('#'+opts.pjax.id).contents().children('div[data-key]');
			mata.infinitePager.nextPageUrl = $('#'+opts.pjax.id).contents().children('ul.pagination').find('li.next a').attr('href');
			e.stopPropagation();

			mata.infinitePager.bodyHeight = $('body').scrollTop()

			return false;
		});

	}
	
}

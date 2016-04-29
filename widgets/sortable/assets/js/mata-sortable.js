$(window).load(function() {

    $(document).on('mouseenter', 'ul.sortable.grid li div.grid-item figure.effect-winston', function() {
        $(this).addClass('hover')
    }).on('mouseleave', 'ul.sortable.grid li div.grid-item figure.effect-winston', function() {
        $(this).removeClass('hover')
    });

});

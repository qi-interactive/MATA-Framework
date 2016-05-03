$(window).load(function() {

    $(document).on('mouseenter', 'figure.effect-winston', function() {
        $(this).addClass('hover')
    }).on('mouseleave', 'figure.effect-winston', function() {
        $(this).removeClass('hover')
    });

});

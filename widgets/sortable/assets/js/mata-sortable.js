$(window).load(function() {

    $(document).on('mousedown', 'ul.sortable.grid li div.grid-item figure', function(e) {
        if($(e.target).parents('a').length == 0)
            $('figcaption', this).hide();
    });

    $(document).on('mouseup dragend.h5s', 'ul.sortable.grid', function() {
        $('ul.sortable.grid li div.grid-item figure figcaption').show();
    });

});

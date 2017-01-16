$(document).ready(function(){

    $('a.add-to-basket').click(function(e) {
        e.preventDefault();
        var anchor = $(this);
        $.get(anchor.attr('href'), {}, function(response){
            if (response.success == true) {
                $('span.basket-count').each(function() {
                    $(this).html(response.data.newCount);
                });
                anchor.next().show();
                anchor.remove();
            }
        });
    });
});

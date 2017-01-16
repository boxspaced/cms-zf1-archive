$(document).ready(function(){

    $('ul:not(#sitemap) a.hidden-menu-item').each(function() {
        $(this).parent().remove();
    });

    $('ul.navigation').each(function() {
        var ul = $(this);
        if (ul.children().length == 0) {
            ul.remove();
        }
    });

});

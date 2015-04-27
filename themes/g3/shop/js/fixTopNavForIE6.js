(function (){
    $(function () {
        var menus = $('#site-nav').find('.menu');
        //判断是否为IE6
        if ($.browser.msie && ($.browser.version == "6.0") && !$.support.style) {
            menus.each(function () {
                $(this).mouseover(function () {
                    $(this).find('.menu-hd').addClass('ie6hd topnav-cur');
                    $(this).find('.menu-hd').find('b').addClass('ie6b');
                    $(this).find('.menu-bd').show();
                });
                $(this).mouseout(function () {
                    $(this).find('.menu-hd').removeClass('ie6hd topnav-cur');
                    $(this).find('.menu-hd').find('b').removeClass('ie6b');
    //                    $(this).find('.menu-hd').find('b').show();
                    $(this).find('.menu-bd').hide();
                });
            });
        }
    });
})();
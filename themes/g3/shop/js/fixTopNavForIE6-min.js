(function(){$(function(){var a=$("#site-nav").find(".menu");if($.browser.msie&&($.browser.version=="6.0")&&!$.support.style){a.each(function(){$(this).mouseover(function(){$(this).find(".menu-hd").addClass("ie6hd topnav-cur");$(this).find(".menu-hd").find("b").addClass("ie6b");$(this).find(".menu-bd").show()});$(this).mouseout(function(){$(this).find(".menu-hd").removeClass("ie6hd topnav-cur");$(this).find(".menu-hd").find("b").removeClass("ie6b");$(this).find(".menu-bd").hide()})})}})})();
<script>

(function($){
	$.fn.bubbleup = function(options) {
		//Extend the default options of plugin
		var opt = $.extend({}, $.fn.bubbleup.defaults, options),tip = null;
		
		return this.each(function() {
			var w=$(this).width();
			$(this).mouseover(function(){
				if(opt.tooltip) {
					tip = $('<div>' + $(this).attr('alt') + '</div>').css({
						fontFamily: opt.fontFamily,
						color: opt.color, 
						fontSize: opt.fontSize, 
						fontWeight: opt.fontWeight, 
						position: 'absolute', 
						zIndex: 100000
					}).remove().css({top:0,left: 0,visibility:'hidden',display:'block'}).appendTo(document.body);
					var position = $.extend({},$(this).offset(),{width:this.offsetWidth,height:this.offsetHeight}),tipWidth = tip[0].offsetWidth, tipHeight = tip[0].offsetHeight;
					tip.stop().css({
						top: position.top - tipHeight, 
						left: position.left + position.width / 2 - tipWidth / 2,
						visibility: 'visible'
					}).animate({top:'-='+(opt.scale/2-w/2)},opt.inSpeed); 
				}
				
				$(this).closest('li').css({'z-index':100000});
				
				$(this).stop().css({'z-index':100000,'top':0,'left':0,'width':w}).animate({
					left:-opt.scale/2+w/2,
					top:-opt.scale/2+w/2,
					width:opt.scale
				},opt.inSpeed);
			}).mouseout(function(){
				
				$(this).closest('li').css({'z-index':100});
				$(this).closest('li').next().css({'z-index':0});
				$(this).closest('li').next().css({'z-index':0});
				$(this).closest('li').next().children('img').css({'z-index':0});
				
				if(opt.tooltip){tip.remove()}
				$(this).stop().animate({left:0,top:0,width:w},opt.outSpeed,function(){
					$(this).css({'z-index':0});
					
				});
			})
		})
	}
	$.fn.bubbleup.defaults = {
		tooltip: false,
		scale:96,
		fontFamily:'Helvetica, Arial, sans-serif',
		color:'#333333',
		fontSize:12,
		fontWeight:'bold',
		inSpeed:'fast',
		outSpeed:'fast'
	}
})(jQuery);
</script>
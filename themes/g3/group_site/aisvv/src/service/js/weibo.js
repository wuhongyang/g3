$(function(){
	//播放视频
	$('.videoimg').click(function(){
		var videosrc = $(this).attr('title');
		var str = '<p><a style = "color:#0078B6;cursor:pointer;" class="colsevideo">收起</a></p><embed height="400" width="480" align="middle" type="application/x-shockwave-flash" allowscriptaccess="always" quality="high" flashvars="playMovie=true&isAutoPlay=true&auto=1&autoPlay=true&adss=0" allowfullscreen="true" src="'+videosrc+'" ></embed>';
		$(this).next('.videopath').html(str);
		$(this).css('display','none');
	})
	//发布微薄验证长度
	$('.fabu').click(function(){
		
		var num = $('#tbody').val().length;
		if( num > 140 )
		{
			alert('输入内容不能超过140个汉字');
			return false;
		}
		if( num < 1 )
		{
			alert('输入的内容不能为空');
			return false;
		}
	})
	$('.pubweibo').click(function(){
		alert('dklasjf');
	})
	//移除粉丝
	$('.fansremove').click(function(){
		var id = $(this).attr('follow');
		var move = confirm('确定移除？');
	       	obj = $(this);
		if( move )
		{
			var url = "fans.php?module=movefans&id="+id;
			$.get(url,function(d){
				if( d == '1' )
				{
					var a =obj.parent().parent().parent().parent().animate({"height":'toggle', "opacity":'toggle'});
				}
				else
					alert('移除失败');
			}); 
		}
		else
		{
			return false;
		}

	})
	//移除关注
	$('.followremove').click(function(){
			var id = $(this).attr('title');
			var move = confirm('确定取消关注？');
			obj = $(this);
			if( move )
			{
				var url = "fans.php?module=movefollow&id="+id;
				$.get(url,function(d){
					if( d == '1' )
						var a =obj.parent().parent().parent().parent().animate({"height":'toggle', "opacity":'toggle'});
					else
						alert('取消失败');
				});
			}
	})
	//点击关注
	$('.clickguan').click(function(){
		var obj = $(this);
		var follow = $(this).attr('value');
		$.post('fans.php?module=addfollow',{id:follow},function(d){
			if( d == '1' )
				obj.attr('src', obj.attr('src1'));
			else
				alert('已关注该用户');
		})
	})
	//点击微博删除
	$('.delweibo').click(function(){
		if( confirm('确定删除?') ){
			var url = $(this).attr('href');
			var obj = $(this);
			$.get(url,function(d){
				obj.parent().parent().parent().parent().animate({"height":'toggle', "opacity":'toggle'});
			});
		}
		return false;
	})
	//取消关注
	$('.quxiaoguanzhu').click(function(){
			var obj = $(this);
			var id = $(this).attr('value');
			$.get('fans.php?module=movefollow&id='+id,function(d){
					obj.attr('src',obj.attr('src1'));
			});
	})
})

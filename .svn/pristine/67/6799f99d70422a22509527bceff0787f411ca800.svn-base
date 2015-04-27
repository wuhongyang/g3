$(function(){
	//爱好添加操作
	$('.onehobby').livequery('click', function(){
		var obj = $(this);
		var hobby = $(this).text();
		var hobbyid = $(this).attr('hobby');
		var appdiv = "<div class = 'myinter_1' hobby = "+hobbyid+" >"+hobby+"<input type = 'hidden' name = 'hobby[]' value = "+hobbyid+"></div>";
		$(this).remove();
		$('#nowinteres').append(appdiv);

	})

	//爱好移除操作
	$('.myinter_1').livequery('click', function() { 

		var hobby = $(this).text();
		var hobbyid = $(this).attr('hobby');
		var appdiv = "<div class = 'onehobby' hobby = "+hobbyid+">"+hobby+"<input type = 'hidden' name = 'hobby[]' value = "+hobbyid+"></div>";
		$('#allcontent').append(appdiv);
		$(this).remove();

    	}); 
})



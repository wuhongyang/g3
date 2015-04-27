(function(){
	var user_status = 1;
	$('#otherUser').click(function(){
		$('#formMsg span').hide();
		var cu = $(this).attr('charge-user');
		if(!cu){
			$(this).html('给自己充值');
			$(this).attr('charge-user',$('#uin').val());
			$(this).css('color','#ED4D01');
			$('#uin').removeClass('readonly');
			$('#uin').removeAttr('readonly');
			$('#uin').css('border','solid 1px #CCC');
			$('#uin').focus();
		}else{
			$(this).html('给好友充值');
			$('#uin').val(cu);
			$('#uin').css('border','none');
			$('#uin').addClass('readonly');
			$('#uin').attr('readonly','readonly');
			$(this).attr('charge-user','');
			$(this).css('color','#0370DA');
			$('#uin').css('border','solid 1px #CCC');

			checkUser(cu);
		}
	});

	$('#uin').change(function(){
		checkUser($(this).val());
	});

	$('.other-sum').click(function(e){
		e.preventDefault();
	 	e.stopPropagation();
		$('.other-sum-list').toggle();
	});

	$("body").click(function(e){
		$('.other-sum-list').hide();
	});

	$('.other-sum-list a').click(function(){
		var money = parseInt($(this).html(),10);
		$('#money').val(money);
		$('.other-sum-list').hide();
		$('#money').keyup();
	});

	$('#money').keyup(function(){
		var money = $(this).val();
		money = money.replace(/[^\d]/g,'');
		var unit = $('#unit').attr('weight') || 10000;
		$(this).val(money);
		$('#total_vdian').html(money*getRebate()/unit);
	})

	function checkUser(uin){
		if(uin > 0){
			$.ajax({
				url:"/shop/index.php?module=getUserInfo",
				type:'get',
				data: {uin:uin},
				async: false,
				success:function(data){
					var nick = data.split('|')[1];
					if(data != '|'){
						user_status = 1;
						$('#Nick').html(nick+' <i class="icon-success"></i>');
					}else{
						user_status = 0;
						setFormError('充值帐号不存在，请重新输入');
						$('#uin').css('border','solid 1px #ED4D01');
						$('#Nick').html(uin+' <i class="icon-error"></i>');
					}
				},
				error:function(){
					setFormError('用户验证失败，请稍候再试！');
				}
			});
		}else{
			user_status = 0;
			$('#uin').css('border','solid 1px #ED4D01');
			setFormError('充值帐号必须是数字');
			$(this).focus();
		}
	}

	function setFormError(msg){
		$('#formMsg').html('<span>'+msg+'</span>');
		$('#formMsg span').fadeOut(5000);
	}

	$('#J_recharge').click(function(){
		if($('#money').val() < 10){
			_show_msg('充值金额必须大于等于10的整数');
			return false;
		}
		checkParam();
	})

	$('#J_post').click(function(){
		checkParam();
	});

	function checkParam(){
		if(user_status != 1){
			checkUser($('#uin').val());
			_show_msg('充值账号有误');
			return false;
		}
		if($('#money').val() == ''){
			_show_msg('金额不能为空');
			return false;
		}
		$('#form').submit();
	}
	
})();
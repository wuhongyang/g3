(function () {
	$(function () {
		var friend_ckb = $('#J_friend_ckb'),
				friend_li = $('#J_friend_input'),
				friend_inputs = friend_li.find('input'),
				error = $('#J_error');
		friend_ckb.click(function () {
			if (friend_li.hasClass('vh') && $(this).is(':checked')) {
				friend_li.removeClass('vh');
				friend_inputs.removeAttr('disabled').val('').eq(0).focus();
				$('#J_checkuser').html('').removeClass();
			} else {
				friend_li.addClass('vh');
				friend_inputs.attr('disabled', 'disabled');

			}
		});
		
		friend_inputs.eq(0).blur(checkFriendID);
		
		//验证好友ID
		function checkFriendID() {
			var friend_id = friend_inputs[0].value;
			if (friend_id == "") {
				return false;
			}
			$.ajax({
				type:'GET',
				url:'index.php',
				dataType:'text',
				async:true,
				data:{
					'module':'getUserInfo',
					'uin':friend_id
				},
				success:function (data) {
					handleData(data, friend_id);
				}
			});
		}
		//验证两次输入好友ID
		function again() {
			var f1 = friend_inputs[0].value,
					f2 = friend_inputs[1].value;

			if (!f1 || !f2) {
				error.html('购买ID或确认ID不能为空');
				friend_inputs[0].focus();
				return false;
			}
			if (f1 != f2) {
				error.html('两次输入的用户ID不一致');
				friend_inputs[0].focus();
				return false;
			}
			return true;
		}
		window.again = again;
	});
})();

 //处理ajax返回数据
function handleData(data, friend_id) {
	var uid = data.split('|')[0];
	var unick = data.split('|')[1];
	if (uid == friend_id) {
		$('#J_checkuser').attr('flag','true').removeClass('friend-wrong').addClass('friend-right').html('');
		$('#J_nick').html(unick);
		handleData.flag = true;
	} else {
		$('#J_checkuser').attr('flag','false').removeClass('friend-right').addClass('friend-wrong').html('账号不存在，请重新输入');
		friend_inputs[0].focus();
        $('#J_checkuser').attr('flag','false');
		handleData.flag = false;
	}
}


